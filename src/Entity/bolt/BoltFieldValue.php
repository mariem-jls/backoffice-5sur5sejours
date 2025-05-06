<?php

namespace App\Entity\bolt;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoltFieldValue
 *
 * @ORM\Table(name="bolt_field_value", indexes={@ORM\Index(name="IDX_8B31D78784A0A3ED745E1826", columns={"content_id", "contenttype"})})
 * @ORM\Entity
 */
class BoltFieldValue
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="contenttype", type="string", length=64, nullable=false)
     */
    private $contenttype;

    /**
     * @var int
     *
     * @ORM\Column(name="content_id", type="integer", nullable=false)
     */
    private $contentId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="grouping", type="integer", nullable=false)
     */
    private $grouping = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="block", type="string", length=64, nullable=true)
     */
    private $block;

    /**
     * @var string
     *
     * @ORM\Column(name="fieldname", type="string", length=255, nullable=false)
     */
    private $fieldname;

    /**
     * @var string
     *
     * @ORM\Column(name="fieldtype", type="string", length=255, nullable=false)
     */
    private $fieldtype;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_string", type="string", length=255, nullable=true)
     */
    private $valueString;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_text", type="text", length=0, nullable=true)
     */
    private $valueText;

    /**
     * @var int|null
     *
     * @ORM\Column(name="value_integer", type="integer", nullable=true)
     */
    private $valueInteger;

    /**
     * @var float|null
     *
     * @ORM\Column(name="value_float", type="float", precision=10, scale=0, nullable=true)
     */
    private $valueFloat;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_decimal", type="decimal", precision=18, scale=9, nullable=true)
     */
    private $valueDecimal;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="value_date", type="date", nullable=true)
     */
    private $valueDate;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="value_datetime", type="datetime", nullable=true)
     */
    private $valueDatetime;

    /**
     * @var json
     *
     * @ORM\Column(name="value_json_array", type="json", nullable=false)
     */
    private $valueJsonArray;

    /**
     * @var bool
     *
     * @ORM\Column(name="value_boolean", type="boolean", nullable=false)
     */
    private $valueBoolean = '0';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenttype(): ?string
    {
        return $this->contenttype;
    }

    public function setContenttype(string $contenttype): self
    {
        $this->contenttype = $contenttype;

        return $this;
    }

    public function getContentId(): ?int
    {
        return $this->contentId;
    }

    public function setContentId(int $contentId): self
    {
        $this->contentId = $contentId;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGrouping(): ?int
    {
        return $this->grouping;
    }

    public function setGrouping(int $grouping): self
    {
        $this->grouping = $grouping;

        return $this;
    }

    public function getBlock(): ?string
    {
        return $this->block;
    }

    public function setBlock(?string $block): self
    {
        $this->block = $block;

        return $this;
    }

    public function getFieldname(): ?string
    {
        return $this->fieldname;
    }

    public function setFieldname(string $fieldname): self
    {
        $this->fieldname = $fieldname;

        return $this;
    }

    public function getFieldtype(): ?string
    {
        return $this->fieldtype;
    }

    public function setFieldtype(string $fieldtype): self
    {
        $this->fieldtype = $fieldtype;

        return $this;
    }

    public function getValueString(): ?string
    {
        return $this->valueString;
    }

    public function setValueString(?string $valueString): self
    {
        $this->valueString = $valueString;

        return $this;
    }

    public function getValueText(): ?string
    {
        return $this->valueText;
    }

    public function setValueText(?string $valueText): self
    {
        $this->valueText = $valueText;

        return $this;
    }

    public function getValueInteger(): ?int
    {
        return $this->valueInteger;
    }

    public function setValueInteger(?int $valueInteger): self
    {
        $this->valueInteger = $valueInteger;

        return $this;
    }

    public function getValueFloat(): ?float
    {
        return $this->valueFloat;
    }

    public function setValueFloat(?float $valueFloat): self
    {
        $this->valueFloat = $valueFloat;

        return $this;
    }

    public function getValueDecimal(): ?string
    {
        return $this->valueDecimal;
    }

    public function setValueDecimal(?string $valueDecimal): self
    {
        $this->valueDecimal = $valueDecimal;

        return $this;
    }

    public function getValueDate(): ?\DateTimeInterface
    {
        return $this->valueDate;
    }

    public function setValueDate(?\DateTimeInterface $valueDate): self
    {
        $this->valueDate = $valueDate;

        return $this;
    }

    public function getValueDatetime(): ?\DateTimeInterface
    {
        return $this->valueDatetime;
    }

    public function setValueDatetime(?\DateTimeInterface $valueDatetime): self
    {
        $this->valueDatetime = $valueDatetime;

        return $this;
    }

    public function getValueJsonArray(): ?array
    {
        return $this->valueJsonArray;
    }

    public function setValueJsonArray(array $valueJsonArray): self
    {
        $this->valueJsonArray = $valueJsonArray;

        return $this;
    }

    public function getValueBoolean(): ?bool
    {
        return $this->valueBoolean;
    }

    public function setValueBoolean(bool $valueBoolean): self
    {
        $this->valueBoolean = $valueBoolean;

        return $this;
    }


}
