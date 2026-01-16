<?php

namespace App\Model;

use App\Model\Exceptions\InvalidCodeException;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\DuplicatedDataException;

class CountryScenarios {
    public function __construct(
        private readonly CountryRepository $storage
    ) {}

    // getAll - получение всех стран
    // вход: -
    // выход: массив объектов Country
    public function getAll(): array {
        return $this->storage->selectAll();
    }

    // get - получение страны по коду
    // вход: код страны (alpha2, alpha3 или numeric)
    // выход: объект Country
    // исключения: InvalidCodeException, CountryNotFoundException
    public function get(string $code): Country {
        if (!$this->validateCode($code)) {
            throw new InvalidCodeException($code, 'validation failed');
        }
        
        $country = $this->determineAndGetByCode($code);
        if ($country === null) {
            throw new CountryNotFoundException($code);
        }
        
        return $country;
    }

    // store - сохранение новой страны
    // вход: объект Country
    // выход: -
    // исключения: InvalidCodeException, DuplicatedDataException
    public function store(Country $country): void {
        // Валидация кодов
        if (!$this->validateAlpha2($country->isoAlpha2)) {
            throw new InvalidCodeException($country->isoAlpha2, 'Invalid ISO Alpha-2 code');
        }
        if (!$this->validateAlpha3($country->isoAlpha3)) {
            throw new InvalidCodeException($country->isoAlpha3, 'Invalid ISO Alpha-3 code');
        }
        if (!$this->validateNumeric($country->isoNumeric)) {
            throw new InvalidCodeException($country->isoNumeric, 'Invalid ISO numeric code');
        }

        // Проверка уникальности
        if ($this->storage->existsByAlpha2($country->isoAlpha2)) {
            throw new DuplicatedDataException('alpha2', $country->isoAlpha2);
        }
        if ($this->storage->existsByAlpha3($country->isoAlpha3)) {
            throw new DuplicatedDataException('alpha3', $country->isoAlpha3);
        }
        if ($this->storage->existsByNumeric($country->isoNumeric)) {
            throw new DuplicatedDataException('numeric', $country->isoNumeric);
        }
        if ($this->storage->existsByName($country->shortName, $country->fullName)) {
            throw new DuplicatedDataException('name', $country->shortName);
        }

        // Проверка числовых значений
        if ($country->population < 0) {
            throw new \InvalidArgumentException('Population cannot be negative');
        }
        if ($country->square < 0) {
            throw new \InvalidArgumentException('Square cannot be negative');
        }

        $this->storage->save($country);
    }

    // edit - редактирование страны по коду
    // вход: код страны, объект Country (без кодов)
    // выход: -
    // исключения: InvalidCodeException, CountryNotFoundException, DuplicatedDataException
    public function edit(string $code, Country $country): void {
        if (!$this->validateCode($code)) {
            throw new InvalidCodeException($code, 'validation failed');
        }

        $existingCountry = $this->determineAndGetByCode($code);
        if ($existingCountry === null) {
            throw new CountryNotFoundException($code);
        }

        // Проверка уникальности имен
        if ($country->shortName !== $existingCountry->shortName && 
            $this->storage->existsByName($country->shortName, $country->fullName)) {
            throw new DuplicatedDataException('name', $country->shortName);
        }

        // Сохраняем оригинальные коды
        $country->isoAlpha2 = $existingCountry->isoAlpha2;
        $country->isoAlpha3 = $existingCountry->isoAlpha3;
        $country->isoNumeric = $existingCountry->isoNumeric;

        $this->storage->update($code, $country);
    }

    // delete - удаление страны по коду
    // вход: код страны
    // выход: -
    // исключения: InvalidCodeException, CountryNotFoundException
    public function delete(string $code): void {
        if (!$this->validateCode($code)) {
            throw new InvalidCodeException($code, 'validation failed');
        }

        if (!$this->storage->existsByAnyCode($code)) {
            throw new CountryNotFoundException($code);
        }

        $this->storage->deleteByCode($code);
    }

    private function determineAndGetByCode(string $code): ?Country {
        if ($this->validateAlpha2($code)) {
            return $this->storage->selectByAlpha2($code);
        }
        if ($this->validateAlpha3($code)) {
            return $this->storage->selectByAlpha3($code);
        }
        if ($this->validateNumeric($code)) {
            return $this->storage->selectByNumeric($code);
        }
        return null;
    }

    private function validateCode(string $code): bool {
        return $this->validateAlpha2($code) || 
               $this->validateAlpha3($code) || 
               $this->validateNumeric($code);
    }

    private function validateAlpha2(string $code): bool {
        return preg_match('/^[A-Z]{2}$/', $code);
    }

    private function validateAlpha3(string $code): bool {
        return preg_match('/^[A-Z]{3}$/', $code);
    }

    private function validateNumeric(string $code): bool {
        return preg_match('/^\d{3}$/', $code);
    }
}