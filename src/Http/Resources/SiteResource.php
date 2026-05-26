<?php

namespace Lantera\ExtensionFramework\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'platform' => $this->platform->value,
            'platform_label' => $this->platform->label(),
            'name' => $this->name,
            'url' => $this->url,
            'store_hash' => $this->store_hash,
            'settings' => $this->settings,
            'is_installed' => $this->isInstalled(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'uninstalled_at' => $this->uninstalled_at?->toIso8601String(),
        ];
    }
}
