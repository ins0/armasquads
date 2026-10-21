<?php

declare(strict_types=1);

namespace Armasquads\Tests\Service;

use Armasquads\Model\Member;
use Armasquads\Model\Squad;
use Armasquads\Service\SquadXml;
use PHPUnit\Framework\TestCase;

final class SquadXmlTest extends TestCase
{
    public function testRendersSquadWithMembersLikeTheOldTemplate(): void
    {
        $squad = new Squad(1, 1, 'private', 'logo', '<A&B>', 'Äöü "Quote" & <b>', '0', null, '');
        $members = [
            new Member('76561198000000002', 'Alpha & Co', null, '', null, ''),
            new Member('76561198000000001', 'Zulu', 'Zed', 'z@example.com', '123', '<script>&"'),
        ];

        $expected = "<?xml version=\"1.0\"?>\r\n"
            . "<!DOCTYPE squad SYSTEM \"squad.dtd\">\r\n"
            . "<squad nick=\"&lt;A&amp;B&gt;\">\r\n"
            . "    <name>Äöü &quot;Quote&quot; &amp; &lt;b&gt;</name>\r\n"
            . "    <email>N/A</email>\r\n"
            . "    <web>N/A</web>\r\n"
            . "    <picture>logo.paa</picture>\r\n"
            . "    <title>N/A</title>\r\n"
            . "    <member id=\"76561198000000002\" nick=\"Alpha &amp; Co\">\r\n"
            . "        <name>N/A</name>\r\n"
            . "        <email>N/A</email>\r\n"
            . "        <icq>N/A</icq>\r\n"
            . "        <remark>N/A</remark>\r\n"
            . "    </member>\r\n"
            . "    <member id=\"76561198000000001\" nick=\"Zulu\">\r\n"
            . "        <name>Zed</name>\r\n"
            . "        <email>z@example.com</email>\r\n"
            . "        <icq>123</icq>\r\n"
            . "        <remark>&lt;script&gt;&amp;&quot;</remark>\r\n"
            . "    </member>\r\n"
            . '</squad>';

        self::assertSame($expected, SquadXml::render($squad, $members, 'logo.paa'));
    }

    public function testRendersEmptyPictureWithoutLogo(): void
    {
        $squad = new Squad(1, 1, 'private', null, 'TAG', 'Name', null, 'https://example.com', null);

        self::assertStringContainsString("    <web>https://example.com</web>\r\n    <picture></picture>\r\n", SquadXml::render($squad, [], null));
    }

    public function testDtdHasNoTrailingNewline(): void
    {
        self::assertStringStartsWith('<!ELEMENT squad (name, email, web?, picture?, title?, member+)>', SquadXml::dtd());
        self::assertStringEndsWith('<!ELEMENT remark (#PCDATA)>', SquadXml::dtd());
    }
}
