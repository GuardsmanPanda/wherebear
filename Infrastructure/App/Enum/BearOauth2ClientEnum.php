<?php declare(strict_types=1);

namespace Infrastructure\App\Enum;

use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Crud\BearOauth2ClientCreator;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Enum\BearOauth2ClientTypeEnum;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Service\BearOauth2ClientService;

enum BearOauth2ClientEnum: string {
    case TWITCH = 'TWITCH';
    case GOOGLE = 'GOOGLE';

    public function getDescription(): string {
        return match ($this) {
            self::TWITCH => 'Twitch Client (GuardsmanBob)',
            self::GOOGLE => 'Google OAuth2 Client.',
        };
    }

    public function getClientId(): string {
        return match ($this) {
            self::TWITCH => 'q8q6jjiuc7f2ef04wmb7m653jd5ra8',
            self::GOOGLE => '730408173687-ad7cjtcq30kgm98mtndtot0dc5hv5fjn.apps.googleusercontent.com',
        };
    }

    public function getClientSecret(): string {
        return match ($this) {
            self::TWITCH => 'twitch_client_secret',
            self::GOOGLE => 'google_client_secret',
        };
    }

    public function getOauth2ClientYpe(): BearOauth2ClientTypeEnum {
        return match ($this) {
            self::TWITCH => BearOauth2ClientTypeEnum::TWITCH,
            self::GOOGLE => BearOauth2ClientTypeEnum::GOOGLE,
        };
    }

    public function getRedirectPath(): string {
        return match ($this) {
            self::TWITCH => '/auth/oauth2-client/q8q6jjiuc7f2ef04wmb7m653jd5ra8/callback',
            self::GOOGLE => '/auth/oauth2-client/730408173687-ad7cjtcq30kgm98mtndtot0dc5hv5fjn.apps.googleusercontent.com/callback',
        };
    }

    public static function syncToDatabase(): void {
        foreach (self::cases() as $client) {
            if (BearOauth2ClientService::oauth2ClientExists(clientId: $client->getClientId())) {
                continue;
            }
            BearOauth2ClientCreator::create(
                oauth2_client_id: $client->getClientId(),
                oauth2_client_description: $client->getDescription(),
                oauth2_client_type: $client->getOauth2ClientYpe(),
                oauth2_authorize_uri: $client->getOauth2ClientYpe()->authorizeUri(),
                oauth2_token_uri: $client->getOauth2ClientYpe()->tokenUri(),
                encrypted_oauth2_client_secret: $client->getClientSecret(),
                oauth2_client_redirect_path: $client->getRedirectPath(),
                allow_user_logins: true,
            );
        }
    }
}
