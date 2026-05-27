<?php

namespace Aether;

/**
 * Mailer — SMTP email helper for Honlor admin notifications.
 *
 * Connects to Gmail via STARTTLS on port 587.
 * Used for account-deletion approval / rejection notifications.
 */
class Mailer
{
    private static string $smtpHost  = 'smtp.gmail.com';
    private static int    $smtpPort  = 587;
    private static string $smtpUser  = 'honlorapp@gmail.com';
    private static string $smtpPass  = 'tjoj uoml vikj hjba';
    private static string $fromName  = 'Honlor Security';
    private static string $fromEmail = 'honlorapp@gmail.com';

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Notify a user that their deletion request was approved.
     */
    public static function sendDeletionApproved(string $toEmail, string $toName): bool
    {
        $name    = $toName ?: 'User';
        $subject = 'Your Account Deletion Request Has Been Approved';
        return self::send($toEmail, $toName, $subject, self::approvalTemplate($name));
    }

    /**
     * Notify a user that their deletion request was rejected, with a reason.
     */
    public static function sendDeletionRejected(string $toEmail, string $toName, string $reason): bool
    {
        $name    = $toName ?: 'User';
        $subject = 'Update on Your Account Deletion Request';
        return self::send($toEmail, $toName, $subject, self::rejectionTemplate($name, $reason));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // SMTP Core
    // ─────────────────────────────────────────────────────────────────────────

    private static function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody
    ): bool {
        // SSL context — disable strict peer verification for App Password auth
        $ctx = stream_context_create([
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ],
        ]);

        $sock = @stream_socket_client(
            'tcp://' . self::$smtpHost . ':' . self::$smtpPort,
            $errno, $errstr, 15,
            STREAM_CLIENT_CONNECT,
            $ctx
        );

        if (!$sock) {
            error_log("Mailer: connect failed — $errstr ($errno)");
            return false;
        }

        stream_set_timeout($sock, 15);

        try {
            // ── Greeting ────────────────────────────────────────────────────
            $line = self::readLine($sock);
            if (!self::is($line, 220)) return self::quit($sock, false);

            // ── EHLO ────────────────────────────────────────────────────────
            self::writeLine($sock, 'EHLO ' . (gethostname() ?: 'localhost'));
            self::readMultiLine($sock);   // consume all 250-... lines

            // ── STARTTLS ────────────────────────────────────────────────────
            self::writeLine($sock, 'STARTTLS');
            $line = self::readLine($sock);
            if (!self::is($line, 220)) return self::quit($sock, false);

            // Upgrade plain socket to TLS
            if (!@stream_socket_enable_crypto($sock, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                error_log("Mailer: STARTTLS upgrade failed.");
                return self::quit($sock, false);
            }

            // ── EHLO (re-issue after TLS) ────────────────────────────────────
            self::writeLine($sock, 'EHLO ' . (gethostname() ?: 'localhost'));
            self::readMultiLine($sock);

            // ── AUTH LOGIN ───────────────────────────────────────────────────
            self::writeLine($sock, 'AUTH LOGIN');
            $line = self::readLine($sock);
            if (!self::is($line, 334)) return self::quit($sock, false);

            // App Password: strip spaces for raw transmission
            $pass = str_replace(' ', '', self::$smtpPass);

            self::writeLine($sock, base64_encode(self::$smtpUser));
            $line = self::readLine($sock);
            if (!self::is($line, 334)) return self::quit($sock, false);

            self::writeLine($sock, base64_encode($pass));
            $line = self::readLine($sock);
            if (!self::is($line, 235)) {
                error_log("Mailer: AUTH failed — $line");
                return self::quit($sock, false);
            }

            // ── MAIL FROM ────────────────────────────────────────────────────
            self::writeLine($sock, 'MAIL FROM:<' . self::$fromEmail . '>');
            $line = self::readLine($sock);
            if (!self::is($line, 250)) return self::quit($sock, false);

            // ── RCPT TO ──────────────────────────────────────────────────────
            self::writeLine($sock, 'RCPT TO:<' . $toEmail . '>');
            $line = self::readLine($sock);
            if (!self::is($line, 250)) return self::quit($sock, false);

            // ── DATA ─────────────────────────────────────────────────────────
            self::writeLine($sock, 'DATA');
            $line = self::readLine($sock);
            if (!self::is($line, 354)) return self::quit($sock, false);

            // Build MIME message
            $fromEncoded    = '=?UTF-8?B?' . base64_encode(self::$fromName) . '?=';
            $toEncoded      = $toName
                ? '=?UTF-8?B?' . base64_encode($toName) . '?= <' . $toEmail . '>'
                : $toEmail;
            $subjectEncoded = '=?UTF-8?B?' . base64_encode($subject) . '?=';
            $bodyB64        = chunk_split(base64_encode($htmlBody));

            $message = "From: {$fromEncoded} <" . self::$fromEmail . ">\r\n"
                     . "To: {$toEncoded}\r\n"
                     . "Subject: {$subjectEncoded}\r\n"
                     . "Date: " . date('r') . "\r\n"
                     . "MIME-Version: 1.0\r\n"
                     . "Content-Type: text/html; charset=UTF-8\r\n"
                     . "Content-Transfer-Encoding: base64\r\n"
                     . "\r\n"
                     . $bodyB64
                     . "\r\n.\r\n";

            fwrite($sock, $message);
            $line = self::readLine($sock);

            $ok = self::is($line, 250);
            return self::quit($sock, $ok);

        } catch (\Throwable $e) {
            error_log("Mailer: exception — " . $e->getMessage());
            self::quit($sock, false);
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Socket helpers
    // ─────────────────────────────────────────────────────────────────────────

    private static function writeLine($sock, string $line): void
    {
        fwrite($sock, $line . "\r\n");
    }

    private static function readLine($sock): string
    {
        return (string) fgets($sock, 1024);
    }

    /** Read all continuation lines (e.g. multi-line EHLO 250-...) */
    private static function readMultiLine($sock): string
    {
        $last = '';
        do {
            $last = self::readLine($sock);
        } while (strlen($last) > 3 && $last[3] === '-');
        return $last;
    }

    private static function is(string $line, int $code): bool
    {
        return strncmp($line, (string) $code, 3) === 0;
    }

    private static function quit($sock, bool $result): bool
    {
        if (is_resource($sock)) {
            @fwrite($sock, "QUIT\r\n");
            @fclose($sock);
        }
        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Email Templates
    // ─────────────────────────────────────────────────────────────────────────

    private static function approvalTemplate(string $name): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Account Deletion Approved</title></head>
<body style="margin:0;padding:0;background:#0d0d14;font-family:\'Segoe UI\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0d0d14;padding:48px 16px;">
  <tr><td align="center">
    <table width="580" cellpadding="0" cellspacing="0" style="background:#13131f;border-radius:28px;overflow:hidden;border:1px solid rgba(255,255,255,0.07);">
      <!-- Header -->
      <tr>
        <td style="padding:40px 48px 32px;background:linear-gradient(135deg,#13131f 0%,#1c1040 100%);">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td>
                <div style="display:inline-block;background:rgba(124,106,255,0.12);border:1px solid rgba(124,106,255,0.25);border-radius:18px;padding:14px 18px;margin-bottom:20px;">
                  <span style="font-size:22px;">🔒</span>
                </div>
                <br>
                <span style="color:#ffffff;font-size:22px;font-weight:900;text-transform:uppercase;letter-spacing:3px;">HONLOR</span>
                <span style="color:rgba(124,106,255,0.9);font-size:22px;font-weight:900;text-transform:uppercase;letter-spacing:3px;"> SECURITY</span>
                <br>
                <span style="color:rgba(255,255,255,0.3);font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:5px;">Identity Erasure Protocol</span>
              </td>
              <td align="right" valign="top" style="padding-top:4px;">
                <span style="display:inline-block;background:rgba(16,185,129,0.12);border:1px solid rgba(16,185,129,0.3);color:#10b981;padding:7px 18px;border-radius:100px;font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:3px;">✓ Approved</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Divider -->
      <tr><td style="height:1px;background:linear-gradient(90deg,transparent,rgba(124,106,255,0.3),transparent);"></td></tr>
      <!-- Body -->
      <tr>
        <td style="padding:40px 48px;">
          <p style="color:rgba(255,255,255,0.45);font-size:13px;font-weight:500;margin:0 0 8px;">Hello, <strong style="color:rgba(255,255,255,0.75);">' . htmlspecialchars($name) . '</strong></p>
          <h2 style="color:#ffffff;font-size:22px;font-weight:800;margin:0 0 20px;line-height:1.35;">Your account deletion request<br>has been <span style="color:#10b981;">approved</span>.</h2>
          <p style="color:rgba(255,255,255,0.45);font-size:13px;line-height:1.8;margin:0 0 36px;">We have reviewed and authorized the permanent removal of your account and all associated data from the Honlor platform. Your right to erasure is being processed.</p>

          <table width="100%" cellpadding="0" cellspacing="0" style="background:rgba(16,185,129,0.04);border:1px solid rgba(16,185,129,0.12);border-radius:18px;margin-bottom:32px;">
            <tr><td style="padding:24px 28px;">
              <p style="color:rgba(255,255,255,0.25);font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:4px;margin:0 0 18px;">What happens next</p>
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td style="padding:7px 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                  <span style="color:#10b981;font-size:11px;font-weight:700;">✓</span>
                  <span style="color:rgba(255,255,255,0.55);font-size:12px;margin-left:12px;">Your account data will be permanently deleted within 30 days</span>
                </td></tr>
                <tr><td style="padding:7px 0;border-bottom:1px solid rgba(255,255,255,0.04);">
                  <span style="color:#10b981;font-size:11px;font-weight:700;">✓</span>
                  <span style="color:rgba(255,255,255,0.55);font-size:12px;margin-left:12px;">All personal information will be removed from our servers</span>
                </td></tr>
                <tr><td style="padding:7px 0;">
                  <span style="color:#10b981;font-size:11px;font-weight:700;">✓</span>
                  <span style="color:rgba(255,255,255,0.55);font-size:12px;margin-left:12px;">You will receive a final confirmation once deletion is complete</span>
                </td></tr>
              </table>
            </td></tr>
          </table>

          <p style="color:rgba(255,255,255,0.3);font-size:12px;line-height:1.8;margin:0;">If you wish to cancel this request or retain your account, please contact us immediately at <a href="mailto:honlorapp@gmail.com" style="color:#7c6aff;text-decoration:none;font-weight:600;">honlorapp@gmail.com</a></p>
        </td>
      </tr>
      <!-- Footer -->
      <tr>
        <td style="padding:20px 48px 28px;border-top:1px solid rgba(255,255,255,0.05);">
          <p style="color:rgba(255,255,255,0.15);font-size:11px;margin:0;line-height:1.6;">This is an automated notification from the Honlor Security System. Please do not reply directly to this email.</p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>';
    }

    private static function rejectionTemplate(string $name, string $reason): string
    {
        return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Account Deletion Request Update</title></head>
<body style="margin:0;padding:0;background:#0d0d14;font-family:\'Segoe UI\',Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#0d0d14;padding:48px 16px;">
  <tr><td align="center">
    <table width="580" cellpadding="0" cellspacing="0" style="background:#13131f;border-radius:28px;overflow:hidden;border:1px solid rgba(255,255,255,0.07);">
      <!-- Header -->
      <tr>
        <td style="padding:40px 48px 32px;background:linear-gradient(135deg,#13131f 0%,#1c1040 100%);">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td>
                <div style="display:inline-block;background:rgba(124,106,255,0.12);border:1px solid rgba(124,106,255,0.25);border-radius:18px;padding:14px 18px;margin-bottom:20px;">
                  <span style="font-size:22px;">🔒</span>
                </div>
                <br>
                <span style="color:#ffffff;font-size:22px;font-weight:900;text-transform:uppercase;letter-spacing:3px;">HONLOR</span>
                <span style="color:rgba(124,106,255,0.9);font-size:22px;font-weight:900;text-transform:uppercase;letter-spacing:3px;"> SECURITY</span>
                <br>
                <span style="color:rgba(255,255,255,0.3);font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:5px;">Identity Erasure Protocol</span>
              </td>
              <td align="right" valign="top" style="padding-top:4px;">
                <span style="display:inline-block;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#f87171;padding:7px 18px;border-radius:100px;font-size:9px;font-weight:900;text-transform:uppercase;letter-spacing:3px;">Not Approved</span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <!-- Divider -->
      <tr><td style="height:1px;background:linear-gradient(90deg,transparent,rgba(239,68,68,0.25),transparent);"></td></tr>
      <!-- Body -->
      <tr>
        <td style="padding:40px 48px;">
          <p style="color:rgba(255,255,255,0.45);font-size:13px;font-weight:500;margin:0 0 8px;">Hello, <strong style="color:rgba(255,255,255,0.75);">' . htmlspecialchars($name) . '</strong></p>
          <h2 style="color:#ffffff;font-size:22px;font-weight:800;margin:0 0 20px;line-height:1.35;">We were unable to process<br>your account deletion request.</h2>
          <p style="color:rgba(255,255,255,0.45);font-size:13px;line-height:1.8;margin:0 0 32px;">After reviewing your request, our team has determined that it cannot be processed at this time. Please see the reason below.</p>

          <table width="100%" cellpadding="0" cellspacing="0" style="background:rgba(239,68,68,0.04);border:1px solid rgba(239,68,68,0.15);border-radius:18px;margin-bottom:32px;">
            <tr><td style="padding:24px 28px;">
              <p style="color:rgba(255,255,255,0.25);font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:4px;margin:0 0 12px;">Reason for rejection</p>
              <p style="color:rgba(255,255,255,0.7);font-size:14px;line-height:1.7;margin:0;font-weight:500;">' . htmlspecialchars($reason) . '</p>
            </td></tr>
          </table>

          <table width="100%" cellpadding="0" cellspacing="0" style="background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.06);border-radius:18px;margin-bottom:32px;">
            <tr><td style="padding:20px 28px;">
              <p style="color:rgba(255,255,255,0.25);font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:4px;margin:0 0 12px;">What you can do</p>
              <table cellpadding="0" cellspacing="0" width="100%">
                <tr><td style="padding:6px 0;">
                  <span style="color:#7c6aff;font-size:11px;">→</span>
                  <span style="color:rgba(255,255,255,0.5);font-size:12px;margin-left:10px;">Resolve any outstanding issues with your account</span>
                </td></tr>
                <tr><td style="padding:6px 0;">
                  <span style="color:#7c6aff;font-size:11px;">→</span>
                  <span style="color:rgba(255,255,255,0.5);font-size:12px;margin-left:10px;">Resubmit your request after 24 hours</span>
                </td></tr>
                <tr><td style="padding:6px 0;">
                  <span style="color:#7c6aff;font-size:11px;">→</span>
                  <span style="color:rgba(255,255,255,0.5);font-size:12px;margin-left:10px;">Contact support if you believe this was a mistake</span>
                </td></tr>
              </table>
            </td></tr>
          </table>

          <p style="color:rgba(255,255,255,0.3);font-size:12px;line-height:1.8;margin:0;">For assistance, please contact us at <a href="mailto:honlorapp@gmail.com" style="color:#7c6aff;text-decoration:none;font-weight:600;">honlorapp@gmail.com</a></p>
        </td>
      </tr>
      <!-- Footer -->
      <tr>
        <td style="padding:20px 48px 28px;border-top:1px solid rgba(255,255,255,0.05);">
          <p style="color:rgba(255,255,255,0.15);font-size:11px;margin:0;line-height:1.6;">This is an automated notification from the Honlor Security System. Please do not reply directly to this email.</p>
        </td>
      </tr>
    </table>
  </td></tr>
</table>
</body>
</html>';
    }
}
