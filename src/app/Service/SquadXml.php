<?php

declare(strict_types=1);

namespace Armasquads\Service;

use Armasquads\Model\Member;
use Armasquads\Model\Squad;

/**
 * Renders the squad.xml the game downloads. The format (CRLF line endings, "N/A" for empty values) matches the files
 * generated since 2014, so existing squad URLs keep producing byte-identical output.
 */
final class SquadXml
{
    /**
     * @param list<Member> $members
     */
    public static function render(Squad $squad, array $members, ?string $logoFile): string
    {
        $lines = [
            '<?xml version="1.0"?>',
            '<!DOCTYPE squad SYSTEM "squad.dtd">',
            '<squad nick="' . self::escape($squad->tag) . '">',
            '    <name>' . self::valueOrNa($squad->name) . '</name>',
            '    <email>' . self::valueOrNa($squad->email) . '</email>',
            '    <web>' . self::valueOrNa($squad->homepage) . '</web>',
            '    <picture>' . ($logoFile ?: '') . '</picture>',
            '    <title>' . self::valueOrNa($squad->title) . '</title>',
        ];

        foreach ($members as $member) {
            $lines[] = '    <member id="' . self::escape($member->uuid) . '" nick="' . self::escape($member->username) . '">';
            $lines[] = '        <name>' . self::valueOrNa($member->name) . '</name>';
            $lines[] = '        <email>' . self::valueOrNa($member->email) . '</email>';
            $lines[] = '        <icq>' . self::valueOrNa($member->icq) . '</icq>';
            $lines[] = '        <remark>' . self::valueOrNa($member->remark) . '</remark>';
            $lines[] = '    </member>';
        }

        return implode("\r\n", $lines) . "\r\n</squad>";
    }

    public static function dtd(): string
    {
        return <<<'DTD'
            <!ELEMENT squad (name, email, web?, picture?, title?, member+)>
            <!ATTLIST squad nick CDATA #REQUIRED>
            <!ELEMENT member (name, email, icq?, remark?)>
            <!ATTLIST member id CDATA #REQUIRED nick CDATA #REQUIRED>
            <!ELEMENT name (#PCDATA)>
            <!ELEMENT email (#PCDATA)>
            <!ELEMENT icq (#PCDATA)>
            <!ELEMENT web (#PCDATA)>
            <!ELEMENT picture (#PCDATA)>
            <!ELEMENT title (#PCDATA)>
            <!ELEMENT remark (#PCDATA)>
            DTD;
    }

    private static function valueOrNa(?string $value): string
    {
        return $value ? self::escape($value) : 'N/A';
    }

    private static function escape(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
