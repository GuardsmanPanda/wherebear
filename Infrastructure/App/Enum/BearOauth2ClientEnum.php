<?php declare(strict_types=1);

namespace Infrastructure\App\Enum;

use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Crud\BearOauth2ClientCreator;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Enum\LarabearOauth2ClientTypeEnum;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Model\BearOauth2Client;

enum BearOauth2ClientEnum: string {
    case TWITCH = 'TWITCH';
    case GOOGLE = 'GOOGLE';

    public static function fromRequest(): self {
      return self::from(value: Req::getString(key: 'oauth2_client'));
    }

    public function getDescription(): string {
        return match ($this) {
            self::TWITCH => 'Twitch Client (GuardsmanBob)',
            self::GOOGLE => 'Google OAuth2 Client.',
        };
    }


    public function getId(): string {
        return match ($this) {
            self::TWITCH => 'q8q6jjiuc7f2ef04wmb7m653jd5ra8',
            self::GOOGLE => '730408173687-ad7cjtcq30kgm98mtndtot0dc5hv5fjn.apps.googleusercontent.com',
        };
    }


    public function getOauth2ClientType(): LarabearOauth2ClientTypeEnum {
        return match ($this) {
            self::TWITCH => LarabearOauth2ClientTypeEnum::TWITCH,
            self::GOOGLE => LarabearOauth2ClientTypeEnum::GOOGLE,
        };
    }


    public function getUserRedirectPath(): string {
        return match ($this) {
            self::TWITCH => '/auth/oauth2-client/q8q6jjiuc7f2ef04wmb7m653jd5ra8/callback',
            self::GOOGLE => '/auth/oauth2-client/730408173687-ad7cjtcq30kgm98mtndtot0dc5hv5fjn.apps.googleusercontent.com/callback',
        };
    }


    public static function syncToDatabase(): void {
        foreach (self::cases() as $client) {
            if (BearOauth2Client::find(id: $client->getId()) === null) {
                BearOauth2ClientCreator::syncToDatabase(
                    id: $client->getId(),
                    description: $client->getDescription(),
                    oauth2_client_type: $client->getOauth2ClientType(),
                    encrypted_secret: 'default_encrypted_secret',
                    user_redirect_path: $client->getUserRedirectPath(),
                );
            }
        }
    }
}
