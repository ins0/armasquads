<?php

declare(strict_types=1);

namespace Armasquads\Service;

use Armasquads\Model\Squad;
use Armasquads\Repository\SquadRepository;
use ZipArchive;

/**
 * The files a squad publishes for the game: squad.xml, squad.dtd and the logo as PAA.
 */
final readonly class SquadFiles
{
    public function __construct(
        private SquadRepository $squads,
        private LogoService $logos,
    ) {
    }

    public function xml(Squad $squad): string
    {
        $logo = $this->logos->serverLogo($squad);

        return SquadXml::render($squad, $this->squads->members((int) $squad->id), $logo !== null ? basename($logo) : null);
    }

    /**
     * Writes squad.xml, squad.dtd and the PAA logo into a new ZIP file and returns its path.
     */
    public function zip(Squad $squad): string
    {
        $xml = $this->xml($squad);
        $zipFile = (string) tempnam(sys_get_temp_dir(), 'squad');

        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::OVERWRITE);
        $zip->addFromString('squad.xml', $xml);
        $paa = $this->logos->paaPath($squad);
        if ($paa !== null) {
            $zip->addFile($this->logos->absolutePath($paa), basename($paa));
        }
        $zip->addFromString('squad.dtd', SquadXml::dtd());
        $zip->close();

        return $zipFile;
    }
}
