<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 24. 9. 2020
 * Time: 20:35
 */

declare(strict_types=1);

namespace App\Model;

use Nextras\Orm\Entity\Entity;

/**
 * Class Order
 * @package App\Model
 * @property int $id {primary}
 * @property string $hash
 * @property Order|NULL $order {m:1 Order::$posters}
 * @property int $quantity {default 1}
 * @property float $priceWithTax {default 0}
 * @property int $tax {enum self::TAX_*} {default self::TAX_21}
 * @property \DateTimeImmutable|NULL $date {default now}
 * @property string|NULL $text
 * @property string|NULL $theme {enum self::THEME_*} {default self::THEME_BLACK_BORDER}
 * @property \DateTimeImmutable $createdAt {default now}
 * @property string|NULL $placeText
 * @property string|NULL $dateText
 * @property string|NULL $longitudeText
 * @property string|NULL $latitudeText
 * @property string|NULL $longitude
 * @property string|NULL $latitude
 * @property string|NULL $location
 * @property bool|NULL $milkyWay {default false}
 * @property bool|NULL $lines {default true}
 * @property bool|NULL $grid {default true}
 * @property bool|NULL $names {default false}
 * @property bool|NULL $stars {default true}
 * @property bool|NULL $planets {default false}
 * @property bool|NULL $border {default true}
 */
class Poster extends Entity
{
    const TAX_21 = 21;

    public const THEME_BLACK = "BLACK";
    public const THEME_BLACK_BORDER = "BLACK_BORDER";
    public const THEME_RED = "RED";
    public const THEME_RED_BORDER = "RED_BORDER";
    public const THEME_RED_BLACK_BORDER = "RED_BLACK_BORDER";
    public const THEME_GRADIENT_RED_BLACK = "GRADIENT_RED_BLACK";
    public const THEME_BLUE_BORDER = "BLUE_BORDER";
    public const THEME_BLUE = "BLUE";
    public const THEME_GRADIENT_BLUE_BLACK = "GRADIENT_BLUE_BLACK";
    public const THEME_GRADIENT_PURPLE = "GRADIENT_PURPLE";
    public const THEME_SPACE = "SPACE";
    public const THEME_FLOWERS = "FLOWERS";
    public const THEME_PURPLE_FLOWERS = "PURPLE_FLOWERS";

    public function getBasicThemes()
    {
        return [
            self::THEME_BLACK => 'BLACK',
            self::THEME_RED => 'RED',
            self::THEME_BLUE => 'BLUE'
        ];
    }

    public function getBorderThemes()
    {
        return [
            self::THEME_BLACK_BORDER => 'BLACK_BORDER',
            self::THEME_RED_BORDER => 'RED_BORDER',
            self::THEME_RED_BLACK_BORDER => 'RED_BLACK_BORDER',
            self::THEME_BLUE_BORDER => 'BLUE_BORDER'
        ];
    }

    public function getGradientThemes()
    {
        return [
            self::THEME_GRADIENT_RED_BLACK => 'GRADIENT_RED_BLACK',
            self::THEME_GRADIENT_BLUE_BLACK => 'GRADIENT_BLUE_BLACK',
            self::THEME_GRADIENT_PURPLE => 'GRADIENT_PURPLE'
        ];
    }

    public function getPictureThemes()
    {
        return [
            self::THEME_PURPLE_FLOWERS => 'PURPLE_FLOWERS',
            self::THEME_FLOWERS => 'FLOWERS',
            self::THEME_SPACE => 'SPACE'
        ];
    }

    public function getTagLine()
    {
        return ($this->latitudeText ? $this->latitudeText  : '50.1089629'). '°N / ' . ($this->longitudeText ? $this->longitudeText : '14.4300655') . '°E';
    }

    public function getCzechMonth()
    {
        switch ($this->date->format('m'))
        {
            case 1:
                return 'Ledna';
            case 2:
                return 'Února';
            case 3:
                return 'Března';
            case 4:
                return 'Dubna';
            case 5:
                return 'Května';
            case 6:
                return 'Června';
            case 7:
                return 'Července';
            case 8:
                return 'Srpna';
            case 9:
                return 'Září';
            case 10:
                return 'Října';
            case 11:
                return 'Listopadu';
            case 12:
                return 'Prosince';
        }
    }

    public function getFontColorByTheme()
    {
        switch ($this->theme)
        {
            case self::THEME_BLACK:
            case self::THEME_RED:
            case self::THEME_BLUE:
            case self::THEME_BLACK_BORDER:
            case self::THEME_RED_BORDER:
            case self::THEME_RED_BLACK_BORDER:
            case self::THEME_BLUE_BORDER:
            case self::THEME_GRADIENT_RED_BLACK:
            case self::THEME_GRADIENT_BLUE_BLACK:
            case self::THEME_GRADIENT_PURPLE:
            case self::THEME_SPACE:
                return 'white';
            break;
            case self::THEME_PURPLE_FLOWERS:
            case self::THEME_FLOWERS:
                return 'black';
            break;
        }
    }

    public function getMapColorByTheme()
    {
        switch ($this->theme)
        {
            case self::THEME_BLACK:
            case self::THEME_RED:
            case self::THEME_BLUE:
            case self::THEME_BLACK_BORDER:
            case self::THEME_RED_BORDER:
            case self::THEME_RED_BLACK_BORDER:
            case self::THEME_BLUE_BORDER:
            case self::THEME_GRADIENT_RED_BLACK:
            case self::THEME_GRADIENT_BLUE_BLACK:
            case self::THEME_GRADIENT_PURPLE:
            case self::THEME_SPACE:
            case self::THEME_PURPLE_FLOWERS:
            case self::THEME_FLOWERS:
                return 'black';
            break;
        }
    }

    public function getMapBackgroundColorByTheme()
    {
        switch ($this->theme)
        {
            case self::THEME_BLACK:
            case self::THEME_RED:
            case self::THEME_BLUE:
            case self::THEME_BLACK_BORDER:
            case self::THEME_RED_BORDER:
            case self::THEME_RED_BLACK_BORDER:
            case self::THEME_BLUE_BORDER:
            case self::THEME_GRADIENT_RED_BLACK:
            case self::THEME_GRADIENT_BLUE_BLACK:
            case self::THEME_GRADIENT_PURPLE:
            case self::THEME_SPACE:
            case self::THEME_PURPLE_FLOWERS:
            case self::THEME_FLOWERS:
                return 'black';
            break;
        }
    }
}