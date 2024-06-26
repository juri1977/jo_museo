<?php

namespace PhpOffice\PhpSpreadsheet\Chart;

/**
 * Created by PhpStorm.
 * User: Wiktor Trzonkowski
 * Date: 6/17/14
 * Time: 12:11 PM.
 */
class Axis extends Properties
{
    /**
     * Axis Number.
     *
     * @var mixed[]
     */
    private $axisNumber = [
        'format' => self::FORMAT_CODE_GENERAL,
        'source_linked' => 1,
        'numeric' => null,
    ];

    /**
     * Axis Options.
     *
     * @var mixed[]
     */
    private $axisOptions = [
        'minimum' => null,
        'maximum' => null,
        'major_unit' => null,
        'minor_unit' => null,
        'orientation' => self::ORIENTATION_NORMAL,
        'minor_tick_mark' => self::TICK_MARK_NONE,
        'major_tick_mark' => self::TICK_MARK_NONE,
        'axis_labels' => self::AXIS_LABELS_NEXT_TO,
        'horizontal_crosses' => self::HORIZONTAL_CROSSES_AUTOZERO,
        'horizontal_crosses_value' => null,
    ];

    /**
     * Fill Properties.
     *
     * @var mixed[]
     */
    private $fillProperties = [
        'type' => self::EXCEL_COLOR_TYPE_ARGB,
        'value' => null,
        'alpha' => 0,
    ];

    private const NUMERIC_FORMAT = [
        Properties::FORMAT_CODE_NUMBER,
        Properties::FORMAT_CODE_DATE,
    ];

    /**
     * Get Series Data Type.
     *
     * @param mixed $format_code
     */
    public function setAxisNumberProperties($format_code, ?bool $numeric = null): void
    {
        $format = (string) $format_code;
        $this->axisNumber['format'] = $format;
        $this->axisNumber['source_linked'] = 0;
        if (is_bool($numeric)) {
            $this->axisNumber['numeric'] = $numeric;
        } elseif (in_array($format, self::NUMERIC_FORMAT, true)) {
            $this->axisNumber['numeric'] = true;
        }
    }

    /**
     * Get Axis Number Format Data Type.
     *
     * @return string
     */
    public function getAxisNumberFormat()
    {
        return $this->axisNumber['format'];
    }

    /**
     * Get Axis Number Source Linked.
     *
     * @return string
     */
    public function getAxisNumberSourceLinked()
    {
        return (string) $this->axisNumber['source_linked'];
    }

    public function getAxisIsNumericFormat(): bool
    {
        return (bool) $this->axisNumber['numeric'];
    }

    public function setAxisOption(string $key, ?string $value): void
    {
        if (!empty($value)) {
            $this->axisOptions[$key] = $value;
        }
    }

    /**
     * Set Axis Options Properties.
     */
    public function setAxisOptionsProperties(
        string $axisLabels,
        ?string $horizontalCrossesValue = null,
        ?string $horizontalCrosses = null,
        ?string $axisOrientation = null,
        ?string $majorTmt = null,
        ?string $minorTmt = null,
        ?string $minimum = null,
        ?string $maximum = null,
        ?string $majorUnit = null,
        ?string $minorUnit = null
    ): void {
        $this->axisOptions['axis_labels'] = $axisLabels;
        $this->setAxisOption('horizontal_crosses_value', $horizontalCrossesValue);
        $this->setAxisOption('horizontal_crosses', $horizontalCrosses);
        $this->setAxisOption('orientation', $axisOrientation);
        $this->setAxisOption('major_tick_mark', $majorTmt);
        $this->setAxisOption('minor_tick_mark', $minorTmt);
        $this->setAxisOption('minor_tick_mark', $minorTmt);
        $this->setAxisOption('minimum', $minimum);
        $this->setAxisOption('maximum', $maximum);
        $this->setAxisOption('major_unit', $majorUnit);
        $this->setAxisOption('minor_unit', $minorUnit);
    }

    /**
     * Get Axis Options Property.
     *
     * @param string $property
     *
     * @return ?string
     */
    public function getAxisOptionsProperty($property)
    {
        return $this->axisOptions[$property];
    }

    /**
     * Set Axis Orientation Property.
     *
     * @param string $orientation
     */
    public function setAxisOrientation($orientation): void
    {
        $this->axisOptions['orientation'] = (string) $orientation;
    }

    /**
     * Set Fill Property.
     *
     * @param ?string $color
     * @param ?int $alpha
     * @param ?string $AlphaType
     */
    public function setFillParameters($color, $alpha = null, $AlphaType = self::EXCEL_COLOR_TYPE_ARGB): void
    {
        $this->fillProperties = $this->setColorProperties($color, $alpha, $AlphaType);
    }

    /**
     * Get Fill Property.
     *
     * @param string $property
     *
     * @return string
     */
    public function getFillProperty($property)
    {
        return (string) $this->fillProperties[$property];
    }

    /**
     * Get Line Color Property.
     *
     * @param string $propertyName
     *
     * @return null|int|string
     */
    public function getLineProperty($propertyName)
    {
        return $this->lineProperties['color'][$propertyName];
    }

    /** @var string */
    private $crossBetween = ''; // 'between' or 'midCat' might be better

    public function setCrossBetween(string $crossBetween): self
    {
        $this->crossBetween = $crossBetween;

        return $this;
    }

    public function getCrossBetween(): string
    {
        return $this->crossBetween;
    }
}
