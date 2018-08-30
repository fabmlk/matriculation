<?php
/*
 * Ce fichier fait partie du package Tms.
 *
 * Pour les informations complètes de copyright et de licence,
 * veuillez vous référer au fichier LICENSE distribué avec ce code source.
 */
declare(strict_types=1);

namespace Tms\Matriculation;

class Matriculation
{
    /**
     * @var string
     */
    private $matriculation;

    /**
     * @param string $matriculation
     */
    public function __construct(string $matriculation = null)
    {
        if (null === $matriculation) {
            $matriculation = '';
        }

        $this->matriculation = $matriculation;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->formatMatriculation();
    }

    /**
     * Indicate if matriculation is valid SIV.
     *
     * @return bool
     */
    public function isMatriculationSIV(): bool
    {
        return
            $this->isMatriculationSIVNormal() ||
            $this->isMatriculationSIVWGarage() ||
            $this->isMatriculationSIVWW() ||
            $this->isMatriculationSIVCyclo();
    }

    /**
     * Indicate if matriculation is valid SIV 'Normal'.
     * No I, O and U letter
     * No WW on left block, no SS in any block
     * No 000 number.
     *
     * @return bool
     */
    public function isMatriculationSIVNormal(): bool
    {
        $pattern = '^(?!SS|WW)[A-HJ-NP-TV-Z]{2}(?:\s|-)?(?!000)[0-9]{3}(?:\s|-)?(?!SS)[A-HJ-NP-TV-Z]{2}$';

        return 1 === preg_match('/'.$pattern.'/i', $this->matriculation);
    }

    /**
     * Indicate if matriculation is valid SIV 'W garage'.
     *
     * @return bool
     */
    public function isMatriculationSIVWGarage(): bool
    {
        $pattern = '^W(?:\s|-)?(?!000)[0-9]{3}(?:\s|-)?(?!SS)[A-HJ-NP-TV-Z]{2}$';

        return 1 === preg_match('/'.$pattern.'/i', $this->matriculation);
    }

    /**
     * Indicate if matriculation is valid SIV 'Provisoire'.
     *
     * @return bool
     */
    public function isMatriculationSIVWW(): bool
    {
        $pattern = '^WW(?:\s|-)?(?!000)[0-9]{3}(?:\s|-)?(?!SS)[A-HJ-NP-TV-Z]{2}$';

        return 1 === preg_match('/'.$pattern.'/i', $this->matriculation);
    }

    /**
     * Indicate if matriculation is valid SIV 'Cyclomoteur'
     * between 01/07/2004 and 01/07/2015.
     * Last is DH 123 K (not managed)
     * Number between 11 and 999 (not 10), no 0 lead, no 100 multiple (100, 200, 300, ...)
     * No S/S, T/T, W/W and AA/A series.
     *
     * @return bool
     */
    public function isMatriculationSIVCyclo(): bool
    {
        $pattern = '^[A-HJ-NP-TV-Z]{1,2}(?:\s|-)?(?:(?!0|10)[0-9]{2}|(?!0)[0-9]{3}(?<!00))(?:\s|-)?[A-HJ-NP-TV-Z]$';
        $exclude_SS = '^S(?:\s|-)?[0-9]*(?:\s|-)?S$';
        $exclude_TT = '^T(?:\s|-)?[0-9]*(?:\s|-)?T$';
        $exclude_WW = '^W(?:\s|-)?[0-9]*(?:\s|-)?W$';
        $exclude_AAA = '^AA(?:\s|-)?[0-9]*(?:\s|-)?A$';

        return
            1 === preg_match('/'.$pattern.'/i', $this->matriculation) &&
            0 === preg_match('/'.$exclude_SS.'/i', $this->matriculation) &&
            0 === preg_match('/'.$exclude_TT.'/i', $this->matriculation) &&
            0 === preg_match('/'.$exclude_WW.'/i', $this->matriculation) &&
            0 === preg_match('/'.$exclude_AAA.'/i', $this->matriculation);
    }

    /**
     * Indicate if matriculation is valid SIV
     * Excluding :
     *   IT/TTT (Importations Temporaires)
     *   TT (Transits Temporaires)
     *   WxE (Exportations hors UE)
     *   WxL (Exportations vers l'UE)
     *   CMD/CD/C/K (Diplomatiques)
     *   Administratives, Armées, Agricoles.
     *
     * @return bool
     */
    public function isMatriculationFNI(): bool
    {
        return
            $this->isMatriculationFNINormal() ||
            $this->isMatriculationFNIWgarage() ||
            $this->isMatriculationFNIWW();
    }

    /**
     * Indicate if matriculation is valid FNI 'Normal'.
     * No strict (see http://stni.free.fr/index2_fr.html and http://plaque.free.fr/f_rec5f.html for details).
     *
     * @return bool
     */
    public function isMatriculationFNINormal(): bool
    {
        $pattern = '^(?!0)(?:(?:[0-9]{1,4}(?:\s|-)?(?![DIOW])[A-Z])|(?:[0-9]{1,4}(?:\s|-)?(?!SS|TT|WW)[A-HJ-NP-Z]{2})|(?:[0-9]{1,3}(?:\s|-)?(?!T|W|KKK|MMM|MMW|MWM|MWW)[A-HJ-NP-Z]{3}))(?:\s|-)?(?!20)(?:97[1-6]|0[1-9]|[1-8][0-9]|9[0-5]|2[AB])$';

        return 1 === preg_match('/'.$pattern.'/i', $this->matriculation);
    }

    /**
     * Indicate if matriculation is valid FNI 'W garage'.
     * No strict (see http://stni.free.fr/index2_fr.html for details) but useless.
     *
     * @return bool
     */
    public function isMatriculationFNIWgarage(): bool
    {
        $pattern = '^(?!0)[0-9]{1,4}(?:\s|-)?W(?:\s|-)?(?!20)(?:97[1-6]|0[1-9]|[1-8][0-9]|9[0-5]|2[AB])$';

        return 1 === preg_match('/'.$pattern.'/i', $this->matriculation);
    }

    /**
     * Indicate if matriculation is valid FNI 'Provisoire'.
     * No strict (see http://stni.free.fr/index2_fr.html for details) but useless.
     *
     * @return bool
     */
    public function isMatriculationFNIWW(): bool
    {
        $pattern = '^(?!0)[0-9]{1,4}(?:\s|-)?WW[A-HJ-NP-TV-Z]?(?:\s|-)?(?!20)(?:97[1-6]|0[1-9]|[1-8][0-9]|9[0-5]|2[AB])$';

        return 1 === preg_match('/'.$pattern.'/i', $this->matriculation);
    }

    /**
     * Formatting matriculation without check it.
     *
     * @return string
     */
    public function formatMatriculation(): string
    {
        $matriculation = $this->matriculation;

        // SIV Normal + WW + W garage
        $matriculation = preg_replace(
            '/^([A-Z]{2}|W)(?:\s|-)?([0-9]{3})(?:\s|-)?([A-Z]{2})$/i',
            '$1-$2-$3',
            $matriculation
        );

        // SIV Cyclo
        $matriculation = preg_replace(
            '/^([A-Z]{1,2})(?:\s|-)?([0-9]{2,3})(?:\s|-)?([A-Z]{1})$/i',
            '$1 $2 $3',
            $matriculation
        );

        // FNI
        $matriculation = preg_replace(
            '/^([0-9]+)(?:\s|-)?([A-Z]{1,3})(?:\s|-)?([0-9]{2,3}|2[AB])$/i',
            '$1 $2 $3',
            $matriculation
        );

        return \strtoupper($matriculation);
    }
}
