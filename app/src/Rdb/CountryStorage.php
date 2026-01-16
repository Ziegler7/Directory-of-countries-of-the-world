<?php

namespace App\Rdb;

use mysqli;
use Exception;
use App\Model\Country;
use App\Model\CountryRepository;

class CountryStorage implements CountryRepository {
    public function __construct(
        private readonly SqlHelper $sqlHelper
    ) {}

    public function selectAll(): array {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            
            
            $queryStr = 'SELECT 
                short_name, 
                full_name, 
                iso_alpha2, 
                iso_alpha3, 
                iso_numeric, 
                population, 
                square 
                FROM countries 
                ORDER BY short_name';
                
            $rows = $connection->query($queryStr);
            
            $countries = [];
            while ($row = $rows->fetch_assoc()) {
               
                $countries[] = new Country(
                    shortName: $row['short_name'],
                    fullName: $row['full_name'],
                    isoAlpha2: $row['iso_alpha2'],
                    isoAlpha3: $row['iso_alpha3'],
                    isoNumeric: $row['iso_numeric'],
                    population: (int)$row['population'],
                    square: (float)$row['square']
                );
            }
            return $countries;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function selectByAlpha2(string $alpha2): ?Country {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'SELECT 
                short_name, 
                full_name, 
                iso_alpha2, 
                iso_alpha3, 
                iso_numeric, 
                population, 
                square 
                FROM countries 
                WHERE iso_alpha2 = ?';
                
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $alpha2);
            $query->execute();
            $result = $query->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return new Country(
                    shortName: $row['short_name'],
                    fullName: $row['full_name'],
                    isoAlpha2: $row['iso_alpha2'],
                    isoAlpha3: $row['iso_alpha3'],
                    isoNumeric: $row['iso_numeric'],
                    population: (int)$row['population'],
                    square: (float)$row['square']
                );
            }
            return null;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function selectByAlpha3(string $alpha3): ?Country {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'SELECT 
                short_name, 
                full_name, 
                iso_alpha2, 
                iso_alpha3, 
                iso_numeric, 
                population, 
                square 
                FROM countries 
                WHERE iso_alpha3 = ?';
                
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $alpha3);
            $query->execute();
            $result = $query->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return new Country(
                    shortName: $row['short_name'],
                    fullName: $row['full_name'],
                    isoAlpha2: $row['iso_alpha2'],
                    isoAlpha3: $row['iso_alpha3'],
                    isoNumeric: $row['iso_numeric'],
                    population: (int)$row['population'],
                    square: (float)$row['square']
                );
            }
            return null;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function selectByNumeric(string $numeric): ?Country {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'SELECT 
                short_name, 
                full_name, 
                iso_alpha2, 
                iso_alpha3, 
                iso_numeric, 
                population, 
                square 
                FROM countries 
                WHERE iso_numeric = ?';
                
            $query = $connection->prepare($queryStr);
            $query->bind_param('s', $numeric);
            $query->execute();
            $result = $query->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return new Country(
                    shortName: $row['short_name'],
                    fullName: $row['full_name'],
                    isoAlpha2: $row['iso_alpha2'],
                    isoAlpha3: $row['iso_alpha3'],
                    isoNumeric: $row['iso_numeric'],
                    population: (int)$row['population'],
                    square: (float)$row['square']
                );
            }
            return null;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function existsByAlpha2(string $alpha2): bool {
        return $this->selectByAlpha2($alpha2) !== null;
    }

    public function existsByAlpha3(string $alpha3): bool {
        return $this->selectByAlpha3($alpha3) !== null;
    }

    public function existsByNumeric(string $numeric): bool {
        return $this->selectByNumeric($numeric) !== null;
    }

    public function existsByName(string $shortName, string $fullName): bool {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'SELECT COUNT(*) as count FROM countries WHERE short_name = ? OR full_name = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('ss', $shortName, $fullName);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function existsByAnyCode(string $code): bool {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'SELECT COUNT(*) as count FROM countries 
                         WHERE iso_alpha2 = ? OR iso_alpha3 = ? OR iso_numeric = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('sss', $code, $code, $code);
            $query->execute();
            $result = $query->get_result();
            $row = $result->fetch_assoc();
            return $row['count'] > 0;
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function save(Country $country): void {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'INSERT INTO countries (
                short_name, 
                full_name, 
                iso_alpha2, 
                iso_alpha3, 
                iso_numeric, 
                population, 
                square
            ) VALUES (?, ?, ?, ?, ?, ?, ?)';
            
            $query = $connection->prepare($queryStr);
            $query->bind_param(
                'sssssid',
                $country->shortName,
                $country->fullName,
                $country->isoAlpha2,
                $country->isoAlpha3,
                $country->isoNumeric,
                $country->population,
                $country->square
            );
            
            if (!$query->execute()) {
                throw new Exception('Insert failed: ' . $query->error);
            }
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function update(string $code, Country $country): void {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'UPDATE countries SET 
                short_name = ?, 
                full_name = ?,
                population = ?, 
                square = ?
                WHERE iso_alpha2 = ? OR iso_alpha3 = ? OR iso_numeric = ?';
                
            $query = $connection->prepare($queryStr);
            $query->bind_param(
                'ssidsss',
                $country->shortName,
                $country->fullName,
                $country->population,
                $country->square,
                $code,
                $code,
                $code
            );
            
            if (!$query->execute()) {
                throw new Exception('Update failed: ' . $query->error);
            }
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }

    public function deleteByCode(string $code): void {
        try {
            $connection = $this->sqlHelper->openDbConnection();
            $queryStr = 'DELETE FROM countries WHERE iso_alpha2 = ? OR iso_alpha3 = ? OR iso_numeric = ?';
            $query = $connection->prepare($queryStr);
            $query->bind_param('sss', $code, $code, $code);
            
            if (!$query->execute()) {
                throw new Exception('Delete failed: ' . $query->error);
            }
        } finally {
            if (isset($connection)) {
                $connection->close();
            }
        }
    }
}