<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 24. 9. 2020
 * Time: 20:35
 */

declare(strict_types=1);

namespace App\Model;

use Money\Money;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\HasMany;
use Nextras\Orm\Relationships\OneHasOne;

/**
 * Class Order
 * @package App\Model
 * @property int $id {primary}
 * @property string $hash
 * @property string|NULL $name
 * @property string|NULL $surname
 * @property string|NULL $email
 * @property string|NULL $phone
 * @property string|NULL $street
 * @property string|NULL $city
 * @property string|NULL $zip
 * @property string|NULL $company
 * @property string|NULL $ico
 * @property string|NULL $dic
 * @property string|NULL $deliveryName
 * @property string|NULL $deliverySurname
 * @property string|NULL $deliveryStreet
 * @property string|NULL $deliveryCity
 * @property string|NULL $deliveryZip
 * @property string|NULL $deliveryCompany
 * @property bool|NULL $newsletter
 * @property float $totalPriceWithTax {default 0}
 * @property Money $totalPriceWithTaxMoney {virtual}
 * @property string $paymentMethod {enum self::PAYMENT_METHOD_*} {default self::PAYMENT_METHOD_CARD}
 * @property string $paymentState {enum self::PAYMENT_STATE_*} {default self::PAYMENT_STATE_CREATED}
 * @property string $orderState {enum self::ORDER_STATE_*} {default self::ORDER_STATE_CREATED}
 * @property string $deliveryMethod {enum self::DELIVERY_METHOD_*} {default self::DELIVERY_METHOD_ADDRESS}
 * @property \DateTimeImmutable $createdAt {default now}
 * @property \DateTimeImmutable|NULL $sendAt
 * @property \DateTimeImmutable|NULL $termsAgreementAt
 * @property \DateTimeImmutable|NULL $newsletterAgreementAt
 * @property string|NULL $fakturoidInvoiceId
 * @property string|NULL $fakturoidSubjectId
 * @property int|NULL $comgatePaymentId
 * @property Poster[]|HasMany|NULL $posters {1:m Poster::$order}
 */
class Order extends Entity
{
    const ORDER_STATE_CREATED = "CREATED";

    const PAYMENT_METHOD_CASH = "CASH";
    const PAYMENT_METHOD_CARD = "CARD";

    const PAYMENT_STATE_CREATED = "CREATED";

    const DELIVERY_METHOD_ADDRESS = "ADDRESS";
    const DELIVERY_METHOD_ZASILKOVNA = "ZASILKOVNA";
    const DELIVERY_METHOD_PERSONAL = "PERSONAL";

    public function getAddress()
    {
        return sprintf('%s, %s %s', $this->street, $this->city, $this->zip);
    }

    public function getterTotalPriceWithTaxMoney()
    {
        return Money::CZK(intval($this->totalPriceWithTax));
    }
}