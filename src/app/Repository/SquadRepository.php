<?php

declare(strict_types=1);

namespace Armasquads\Repository;

use Armasquads\Model\Member;
use Armasquads\Model\Squad;
use PDO;

final readonly class SquadRepository
{
    private const string PRIVATE_ID_CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    public function __construct(private PDO $db)
    {
    }

    /**
     * @return list<Squad>
     */
    public function findByUser(int $userId): array
    {
        $statement = $this->db->prepare('SELECT * FROM sqa_squads_6d4c2s WHERE BEN_ID = ? ORDER BY SQA_ID DESC');
        $statement->execute([$userId]);

        return array_map(Squad::fromRow(...), $statement->fetchAll());
    }

    public function findForUser(int $userId, int $squadId): ?Squad
    {
        $statement = $this->db->prepare('SELECT * FROM sqa_squads_6d4c2s WHERE BEN_ID = ? AND SQA_ID = ? LIMIT 1');
        $statement->execute([$userId, $squadId]);
        $row = $statement->fetch();

        return $row ? Squad::fromRow($row) : null;
    }

    /**
     * Squads the user owns or was added to as an editor.
     *
     * @return list<Squad>
     */
    public function findEditableByUser(int $userId): array
    {
        $statement = $this->db->prepare(
            'SELECT * FROM sqa_squads_6d4c2s WHERE BEN_ID = ?'
            . ' OR SQA_ID IN (SELECT SQA_ID FROM sqa_squad_editor WHERE BEN_ID = ?) ORDER BY SQA_ID DESC'
        );
        $statement->execute([$userId, $userId]);

        return array_map(Squad::fromRow(...), $statement->fetchAll());
    }

    public function findEditable(int $userId, int $squadId): ?Squad
    {
        $statement = $this->db->prepare(
            'SELECT * FROM sqa_squads_6d4c2s WHERE SQA_ID = ?'
            . ' AND (BEN_ID = ? OR SQA_ID IN (SELECT SQA_ID FROM sqa_squad_editor WHERE BEN_ID = ?)) LIMIT 1'
        );
        $statement->execute([$squadId, $userId, $userId]);
        $row = $statement->fetch();

        return $row ? Squad::fromRow($row) : null;
    }

    /**
     * @return array<int, string> usernames of the editors by user id
     */
    public function editors(int $squadId): array
    {
        $statement = $this->db->prepare(
            'SELECT u.BEN_ID, u.BEN_Username FROM sqa_squad_editor e JOIN ben_benutzer_91c48c u ON u.BEN_ID = e.BEN_ID'
            . ' WHERE e.SQA_ID = ? ORDER BY u.BEN_Username'
        );
        $statement->execute([$squadId]);

        return array_map(strval(...), $statement->fetchAll(PDO::FETCH_KEY_PAIR));
    }

    /**
     * @param list<int> $squadIds
     * @return array<int, int> number of editors by squad id
     */
    public function editorCounts(array $squadIds): array
    {
        if ($squadIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($squadIds), '?'));
        $statement = $this->db->prepare(
            "SELECT SQA_ID, COUNT(*) FROM sqa_squad_editor WHERE SQA_ID IN ($placeholders) GROUP BY SQA_ID"
        );
        $statement->execute($squadIds);

        return array_map(intval(...), $statement->fetchAll(PDO::FETCH_KEY_PAIR));
    }

    public function addEditor(int $squadId, int $userId): void
    {
        $this->db->prepare('INSERT IGNORE INTO sqa_squad_editor (SQA_ID, BEN_ID) VALUES (?, ?)')->execute([$squadId, $userId]);
    }

    public function removeEditor(int $squadId, int $userId): void
    {
        $this->db->prepare('DELETE FROM sqa_squad_editor WHERE SQA_ID = ? AND BEN_ID = ?')->execute([$squadId, $userId]);
    }

    public function findByPrivateId(string $privateId): ?Squad
    {
        $statement = $this->db->prepare('SELECT * FROM sqa_squads_6d4c2s WHERE SQA_PrivateID = ? LIMIT 1');
        $statement->execute([$privateId]);
        $row = $statement->fetch();

        return $row ? Squad::fromRow($row) : null;
    }

    public function createUniquePrivateId(): string
    {
        $exists = $this->db->prepare('SELECT 1 FROM sqa_squads_6d4c2s WHERE SQA_PrivateID = ? LIMIT 1');

        do {
            $privateId = '';
            for ($i = 0; $i < 32; $i++) {
                $privateId .= self::PRIVATE_ID_CHARS[random_int(0, strlen(self::PRIVATE_ID_CHARS) - 1)];
            }
            $exists->execute([$privateId]);
        } while ($exists->fetchColumn() !== false);

        return $privateId;
    }

    public function insert(Squad $squad): void
    {
        $this->db->prepare(
            'INSERT INTO sqa_squads_6d4c2s (BEN_ID, SQA_PrivateID, SQA_Logo, SQA_Tag, SQA_Name, SQA_Title, SQA_Homepage, SQA_Email)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        )->execute([
            $squad->userId,
            $squad->privateId,
            $squad->logo,
            $squad->tag,
            $squad->name,
            $squad->title,
            $squad->homepage,
            $squad->email,
        ]);

        $squad->id = (int) $this->db->lastInsertId();
    }

    public function update(Squad $squad): void
    {
        $this->db->prepare(
            'UPDATE sqa_squads_6d4c2s SET SQA_Logo = ?, SQA_Tag = ?, SQA_Name = ?, SQA_Title = ?, SQA_Homepage = ?, SQA_Email = ?'
            . ' WHERE SQA_ID = ?'
        )->execute([
            $squad->logo,
            $squad->tag,
            $squad->name,
            $squad->title,
            $squad->homepage,
            $squad->email,
            $squad->id,
        ]);
    }

    public function delete(int $squadId): void
    {
        $this->db->beginTransaction();
        $this->db->prepare('DELETE FROM sqa_squad_member_4de785 WHERE SQA_ID = ?')->execute([$squadId]);
        $this->db->prepare('DELETE FROM sqa_squad_editor WHERE SQA_ID = ?')->execute([$squadId]);
        $this->db->prepare('DELETE FROM sqa_squads_6d4c2s WHERE SQA_ID = ?')->execute([$squadId]);
        $this->db->commit();
    }

    /**
     * @return list<Member>
     */
    public function members(int $squadId): array
    {
        $statement = $this->db->prepare('SELECT * FROM sqa_squad_member_4de785 WHERE SQA_ID = ? ORDER BY MEM_Name ASC');
        $statement->execute([$squadId]);

        return array_map(Member::fromRow(...), $statement->fetchAll());
    }

    /**
     * @param list<int> $squadIds
     * @return array<int, int>
     */
    public function memberCounts(array $squadIds): array
    {
        if ($squadIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($squadIds), '?'));
        $statement = $this->db->prepare(
            "SELECT SQA_ID, COUNT(*) FROM sqa_squad_member_4de785 WHERE SQA_ID IN ($placeholders) GROUP BY SQA_ID"
        );
        $statement->execute($squadIds);

        return array_map(intval(...), $statement->fetchAll(PDO::FETCH_KEY_PAIR));
    }

    public function findMember(int $squadId, string $uuid): ?Member
    {
        $statement = $this->db->prepare('SELECT * FROM sqa_squad_member_4de785 WHERE SQA_ID = ? AND MEM_UID = ? LIMIT 1');
        $statement->execute([$squadId, $uuid]);
        $row = $statement->fetch();

        return $row ? Member::fromRow($row) : null;
    }

    /**
     * @param list<Member> $members
     */
    public function replaceMembers(int $squadId, array $members): void
    {
        $this->db->beginTransaction();
        $this->db->prepare('DELETE FROM sqa_squad_member_4de785 WHERE SQA_ID = ?')->execute([$squadId]);
        foreach ($members as $member) {
            $this->insertMember($squadId, $member);
        }
        $this->db->commit();
    }

    public function insertMember(int $squadId, Member $member): void
    {
        $this->db->prepare(
            'INSERT INTO sqa_squad_member_4de785 (SQA_ID, MEM_UID, MEM_Username, MEM_Name, MEM_Email, MEM_ICQ, MEM_Remark)'
            . ' VALUES (?, ?, ?, ?, ?, ?, ?)'
        )->execute([$squadId, $member->uuid, $member->username, $member->name, $member->email, $member->icq, $member->remark]);
    }

    public function updateMember(int $squadId, string $uuid, Member $member): void
    {
        $this->db->prepare(
            'UPDATE sqa_squad_member_4de785 SET MEM_UID = ?, MEM_Username = ?, MEM_Name = ?, MEM_Email = ?, MEM_ICQ = ?, MEM_Remark = ?'
            . ' WHERE SQA_ID = ? AND MEM_UID = ?'
        )->execute([$member->uuid, $member->username, $member->name, $member->email, $member->icq, $member->remark, $squadId, $uuid]);
    }

    public function deleteMember(int $squadId, string $uuid): void
    {
        $this->db->prepare('DELETE FROM sqa_squad_member_4de785 WHERE SQA_ID = ? AND MEM_UID = ?')->execute([$squadId, $uuid]);
    }

    /**
     * @return array{squads: int, members: int, logos: int}
     */
    public function statistics(): array
    {
        return [
            'squads' => (int) $this->db->query('SELECT COUNT(SQA_ID) FROM sqa_squads_6d4c2s')->fetchColumn(),
            'members' => (int) $this->db->query('SELECT COUNT(SQA_ID) FROM sqa_squad_member_4de785')->fetchColumn(),
            'logos' => (int) $this->db->query(
                "SELECT COUNT(SQA_Logo) FROM sqa_squads_6d4c2s WHERE SQA_Logo IS NOT NULL AND SQA_Logo != ''"
            )->fetchColumn(),
        ];
    }
}
