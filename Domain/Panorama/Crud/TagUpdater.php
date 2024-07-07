<?php declare(strict_types=1);

namespace Domain\Panorama\Crud;

use Domain\Panorama\Model\Tag;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class TagUpdater {
    public function __construct(private readonly Tag $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromTagEnum(string $tag_enum): self {
        return new self(model: Tag::findOrFail(id: $tag_enum));
    }


    public function setTagDescription(string $tag_description): self {
        $this->model->tag_description = $tag_description;
        return $this;
    }

    public function update(): Tag {
        $this->model->save();
        return $this->model;
    }
}
