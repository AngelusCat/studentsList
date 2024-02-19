<?php

namespace App\Enrollee;

use \PDO;
use App\Utility\OtherExceptionClasses\AttemptingToAccessAnUnresolvedColumn;

class PersonalDataTDG
{
    private PDO $pdo;

    /**
     * @var array<string, list<string>> Список разрешенных ключевых слов
     */

    private array $listOfAllowedKeywords = [
        'columnNames' => ['name', 'surname', 'gender', 'groupNumber', 'email', 'totalPointsUSE', 'yearOfBirth', 'location'],
    ];

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function checkValuesOnWhiteSheet(string $valueType, string $value): bool
    {
        return in_array($value, $this->listOfAllowedKeywords[$valueType]);
    }

    public function addStudent(Enrollee $enrollee): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO personalData (name, surname, gender, groupNumber, email, totalPointsUSE, yearOfBirth, location) VALUES (:name, :surname, :gender, :groupNumber, :email, :totalPointsUSE, :yearOfBirth, :location)");
        $stmt->bindValue(':name', $enrollee->getName());
        $stmt->bindValue(':surname', $enrollee->getSurname());
        $stmt->bindValue(':gender', $enrollee->getGender());
        $stmt->bindValue(':groupNumber', $enrollee->getGroupNumber());
        $stmt->bindValue(':email', $enrollee->getEmail());
        $stmt->bindValue(':totalPointsUSE', $enrollee->getTotalPointsUSE());
        $stmt->bindValue(':yearOfBirth', $enrollee->getYearOfBirth());
        $stmt->bindValue(':location', $enrollee->getLocation());
        $stmt->execute();
    }

    public function getNumberOfRecordsThatContainThisEmail(string $email): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(email) FROM personalData WHERE email=:email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['COUNT(email)'];
    }

    public function getStudentById(string $id): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM personalData WHERE id=:id");
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function updateColumnValueById(string $columnName, mixed $value, int $id): void
    {

        $check = $this->checkValuesOnWhiteSheet('columnNames', $columnName);

        if (!$check) {
            throw new AttemptingToAccessAnUnresolvedColumn("Столбец " . $columnName . " не входит в список названий столбцов, к которым можно обращаться.");
        }

        $stmt = $this->pdo->prepare("UPDATE personalData SET " . $columnName . "=:value WHERE id=:id");
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
    }

    public function countNumberOfAllRecords(): int
    {
        $sql = "SELECT COUNT(*) FROM personalData";
        $result = $this->pdo->query($sql);
        $amount = $result->fetch(PDO::FETCH_ASSOC);
        return $amount['COUNT(*)'];
    }

    public function getSortedRecordsByIdRange(array $idRange, array $printableColumnsArray = ['name', 'surname', 'groupNumber', 'totalPointsUSE'], string $selectedSort = 'pointsUp'): array
    {
        $sortingOptions = [
            'nameUp' => "ORDER BY name",
            'nameDown' => "ORDER BY name DESC",
            'surnameUp' => "ORDER BY surname",
            'surnameDown' => "ORDER BY surname DESC",
            'groupNumberUp' => "ORDER BY groupNumber",
            'groupNumberDown' => "ORDER BY groupNumber DESC",
            'pointsUp' => "ORDER BY totalPointsUSE",
            'pointsDown' => "ORDER BY totalPointsUSE DESC"
        ];

        $currentSqlPart = $sortingOptions['pointsUp'];

        foreach ($sortingOptions as $sortName => $sqlPart) {
            if ($selectedSort === $sortName) {
                $currentSqlPart = $sqlPart;
                break;
            }
        }

        $printableColumns = (count($printableColumnsArray) > 1) ? implode(', ', $printableColumnsArray) : implode(' ', $printableColumnsArray);

        foreach ($printableColumnsArray as $columnName) {
            $check = $this->checkValuesOnWhiteSheet('columnNames', $columnName);
            if ($check) {
                continue;
            } else {
                $printableColumns = 'name, surname, groupNumber, totalPointsUSE';
            }
        }

        $stmt = $this->pdo->prepare("SELECT " . $printableColumns . " FROM personalData WHERE id BETWEEN :idRangeOne AND :idRangeTwo " . $currentSqlPart);
        $stmt->bindValue('idRangeOne', $idRange[0]);
        $stmt->bindValue('idRangeTwo', $idRange[1]);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getSearchResults(mixed $searchWord, array $printableColumnsArray = ['name', 'surname', 'groupNumber', 'totalPointsUSE'], array $fieldsToSearchForArray = ['name', 'surname', 'groupNumber']): ?array
    {
        $printableColumns = (count($printableColumnsArray) > 1) ? implode(', ', $printableColumnsArray) : implode(' ', $printableColumnsArray);

        foreach ($printableColumnsArray as $columnName) {
            $check = $this->checkValuesOnWhiteSheet('columnNames', $columnName);
            if ($check) {
                continue;
            } else {
                $printableColumns = 'name, surname, groupNumber, totalPointsUSE';
            }
        }

        $searchWord = '%' . $searchWord . '%';

        $startOfSqlQuery = "SELECT " . $printableColumns . " FROM personalData WHERE ";

        foreach ($fieldsToSearchForArray as $columnName) {
            $check = $this->checkValuesOnWhiteSheet('columnNames', $columnName);
            if ($check) {
                continue;
            } else {
                $fieldsToSearchForArray = ['name', 'surname', 'groupNumber'];
            }
        }

        foreach ($fieldsToSearchForArray as $key => $fieldToSearchFor) {
            $partsOfSqlQuery [$fieldToSearchFor] = $fieldToSearchFor . " LIKE lower(:searchWord) ";
            if ($key !== array_key_last($fieldsToSearchForArray)) {
                $partsOfSqlQuery [$fieldToSearchFor] = $partsOfSqlQuery [$fieldToSearchFor] . "OR ";
            }
        }

        $endOfSqlQuery = implode(' ', $partsOfSqlQuery);

        $stmt = $this->pdo->prepare($startOfSqlQuery . $endOfSqlQuery);
        $stmt->bindValue(':searchWord', $searchWord);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMinimumId(): int
    {
        $sql = "SELECT MIN(id) FROM personalData";
        $result = $this->pdo->query($sql);
        $finishResult = $result->fetch(PDO::FETCH_ASSOC);
        return $finishResult['MIN(id)'];
    }

    public function getMaximumId(): int
    {
        $sql = "SELECT MAX(id) FROM personalData";
        $result = $this->pdo->query($sql);
        $finishResult = $result->fetch(PDO::FETCH_ASSOC);
        return $finishResult['MAX(id)'];
    }
}