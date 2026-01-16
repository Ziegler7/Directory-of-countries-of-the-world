<?php

namespace App\Model;

interface CountryRepository {
    function selectAll(): array;
    function selectByAlpha2(string $alpha2): ?Country;
    function selectByAlpha3(string $alpha3): ?Country;
    function selectByNumeric(string $numeric): ?Country;
    function existsByAlpha2(string $alpha2): bool;
    function existsByAlpha3(string $alpha3): bool;
    function existsByNumeric(string $numeric): bool;
    function existsByName(string $shortName, string $fullName): bool;
    function existsByAnyCode(string $code): bool;
    function save(Country $country): void;
    function update(string $code, Country $country): void;
    function deleteByCode(string $code): void;
}