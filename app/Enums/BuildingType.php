<?php

namespace App\Enums;

enum BuildingType: string
{
    case Shopping = 'shopping';
    case Living = 'living';
    case Service = 'service';
    case Production = 'production';

    /**
     * Human readable label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::Shopping => __('Shopping'),
            self::Living => __('Residential'),
            self::Service => __('Services'),
            self::Production => __('Production'),
        };
    }

    /**
     * Only living buildings house residents.
     */
    public function providesResidents(): bool
    {
        return $this === self::Living;
    }
    public function isShopping(): bool
    {
        return $this === self::Shopping;
    }
    public function isLiving(): bool
    {
        return $this === self::Living;
    }
    public function isService(): bool
    {
        return $this === self::Service;
    }
    public function isProduction(): bool
    {
        return $this === self::Production;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
