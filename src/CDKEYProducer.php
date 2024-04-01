<?php

declare(strict_types=1);

namespace axios\tools;

class CDKEYProducer
{
    private int $offset;
    private int $code_min = 0;
    private int $code_max;
    private int $mix_min  = 0;
    private int $mix_max;

    private BHDConverter $converter;

    public function __construct(int $ticket_len, int $mixed_str_len, int $offset = 0)
    {
        $this->converter = new BHDConverter();
        if ($ticket_len > 0) {
            $this->code_min = (int) $this->converter->anyToDecimal('1' . str_repeat('0', $ticket_len - 1), 62);
        }
        $this->code_max  = (int) $this->converter->anyToDecimal(str_repeat('Z', $ticket_len), 62);
        if ($mixed_str_len > 0) {
            $this->mix_min = (int) $this->converter->anyToDecimal('1' . str_repeat('0', $mixed_str_len - 1), 62);
        }
        $this->mix_max   = (int) $this->converter->anyToDecimal(str_repeat('Z', $mixed_str_len), 62);
        $this->offset    = $offset;
    }

    public function setOffset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getOne(): string
    {
        ++$this->offset;
        $cdkey = $this->produce($this->offset);
        if ('' === $cdkey) {
            return '';
        }

        return $cdkey;
    }

    public function get(int $number): array
    {
        $res = [];
        while ($number > 0) {
            --$number;
            ++$this->offset;
            $cdkey = $this->produce($this->offset);
            if ('' === $cdkey) {
                break;
            }
            $res[] = $cdkey;
        }

        return $res;
    }

    private function produce($offset): string
    {
        $value = $this->code_min + $offset;
        if ($value < $this->code_max) {
            $str = $this->converter->decimalToAny($value, 62);
            if ($this->mix_max > $this->mix_min) {
                $mix_value = random_int($this->mix_min, $this->mix_max);
                $str .= $this->converter->decimalToAny((string) $mix_value, 62);
            }

            return $str;
        }

        return '';
    }
}
