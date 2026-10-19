<?php

namespace App\Utilities;

class Overrider
{

    public static function load($type)
    {
        $method = 'load' . ucfirst($type);
        static::$method();
    }

    protected static function loadSettings()
    {
        // Timezone
        config(['app.timezone' => get_option('timezone')]);

        // Email
        $email_protocol = get_option('mail_type', 'mail');
        config(['mail.default' => $email_protocol]);

        config(['mail.from.name' => get_option('from_name')]);
        config(['mail.from.address' => get_option('from_email')]);

        if ($email_protocol == 'smtp') {
            $smtp_host = get_option('smtp_host');
            $smtp_port = get_option('smtp_port');
            $smtp_username = get_option('smtp_username');
            $smtp_password = get_option('smtp_password');
            $smtp_encryption = get_option('smtp_encryption');

            // Only configure SMTP if host is provided
            if (!empty($smtp_host)) {
                config(['mail.mailers.smtp.host' => $smtp_host]);
                config(['mail.mailers.smtp.port' => (int) $smtp_port]);
                config(['mail.mailers.smtp.username' => $smtp_username]);
                config(['mail.mailers.smtp.password' => $smtp_password]);
                config(['mail.mailers.smtp.encryption' => $smtp_encryption]);
            } else {
                // If SMTP not configured, use log driver for development
                config(['mail.default' => 'log']);
            }
        }

    }

	protected static function loadSocialSettings()
    {
		//Set Google Login Credentials
		config(['services.google' => [
				'client_id' => get_option('GOOGLE_CLIENT_ID'),
				'client_secret' => get_option('GOOGLE_CLIENT_SECRET'),
				'redirect' => url('login/google/callback'),
			]
		]);

		//Set Facebook Login Credentials
		config(['services.facebook' => [
				'client_id' => get_option('FACEBOOK_CLIENT_ID'),
				'client_secret' => get_option('FACEBOOK_CLIENT_SECRET'),
				'redirect' => url('login/facebook/callback'),
			]
		]);
	}

}
