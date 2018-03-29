<?php

namespace JeroenDesloovere\Geolocation\Result;

class Address
{
    /** @var \stdClass */
    private $result;

    /** @var null|string */
    private $label;

    private function __construct(
        \stdClass $result,
        ?string $label
    ) {
        $this->addressComponents = $result;
        $this->label = $label;
    }

    public static function createFromGoogleResult(\stdClass $result): Address
    {
        return new self(
            $result,
            $result->formatted_address ?? null
        );
    }

    public function getResult(): \stdClass
    {
        return $this->result;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
