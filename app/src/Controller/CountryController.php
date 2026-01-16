<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

use App\Model\Country;
use App\Model\CountryScenarios;
use App\Model\Exceptions\InvalidCodeException;
use App\Model\Exceptions\CountryNotFoundException;
use App\Model\Exceptions\DuplicatedDataException;

#[Route(path: 'api/country', name: 'app_api_country')]
final class CountryController extends AbstractController
{
    public function __construct(
        private readonly CountryScenarios $countries
    ) {}

    
    #[Route(path: '', name: 'app_api_country_get_all', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $countries = $this->countries->getAll();
        return $this->json(data: $countries, status: 200);
    }

    #[Route(path:'/{code}', name:'app_api_country_get', methods: ['GET'])] 
    public function get(string $code): JsonResponse {
        try {
            $country = $this->countries->get($code);
            return $this->json(data: $country, status: 200);
        } catch (InvalidCodeException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'invalidCode' => $ex->invalidCode
            ], 400);
        } catch (CountryNotFoundException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'notFoundCode' => $ex->notFoundCode
            ], 404);
        }
    }

    #[Route(path: '', name: 'app_api_country_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            
            $country = new Country(
                shortName: $data['shortName'],
                fullName: $data['fullName'],
                isoAlpha2: $data['isoAlpha2'],
                isoAlpha3: $data['isoAlpha3'],
                isoNumeric: $data['isoNumeric'],
                population: (int)$data['population'],
                square: (float)$data['square']
            );
            
            $this->countries->store($country);
            return $this->json(data: null, status: 204);
        } catch (\JsonException $ex) {
            return $this->json(['errorCode' => 400, 'errorMessage' => 'Invalid JSON'], 400);
        } catch (\InvalidArgumentException $ex) {
            return $this->json(['errorCode' => 400, 'errorMessage' => $ex->getMessage()], 400);
        } catch (InvalidCodeException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'invalidCode' => $ex->invalidCode
            ], 400);
        } catch (DuplicatedDataException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'field' => $ex->field,
                'value' => $ex->value
            ], 409);
        }
    }

    #[Route(path: '/{code}', name: 'app_api_country_edit', methods: ['PATCH'])]
    public function edit(string $code, Request $request): JsonResponse {
        try {
            
            $existingCountry = $this->countries->get($code);

            $content = $request->getContent();
            if (empty($content)) {
                throw new \InvalidArgumentException('Empty request body');
            }
            
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \InvalidArgumentException('Invalid JSON: ' . json_last_error_msg());
            }
            
        
            $updatedCountry = new Country(
                shortName: $data['shortName'] ?? $existingCountry->shortName,
                fullName: $data['fullName'] ?? $existingCountry->fullName,
                isoAlpha2: $existingCountry->isoAlpha2, 
                isoAlpha3: $existingCountry->isoAlpha3, 
                isoNumeric: $existingCountry->isoNumeric, 
                population: isset($data['population']) ? (int)$data['population'] : $existingCountry->population,
                square: isset($data['square']) ? (float)$data['square'] : $existingCountry->square
            );
            
            $this->countries->edit($code, $updatedCountry);

            return $this->json(data: [
                'shortName' => $updatedCountry->shortName,
                'fullName' => $updatedCountry->fullName,
                'isoAlpha2' => $updatedCountry->isoAlpha2,
                'isoAlpha3' => $updatedCountry->isoAlpha3,
                'isoNumeric' => $updatedCountry->isoNumeric,
                'population' => $updatedCountry->population,
                'square' => $updatedCountry->square
            ], status: 200);
            
        } catch (\JsonException $ex) {
            return $this->json([
                'errorCode' => 400,
                'errorMessage' => 'Invalid JSON format'
            ], 400);
        } catch (\InvalidArgumentException $ex) {
            return $this->json([
                'errorCode' => 400,
                'errorMessage' => $ex->getMessage()
            ], 400);
        } catch (InvalidCodeException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'invalidCode' => $ex->invalidCode
            ], 400);
        } catch (CountryNotFoundException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'notFoundCode' => $ex->notFoundCode
            ], 404);
        } catch (DuplicatedDataException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'field' => $ex->field,
                'value' => $ex->value
            ], 409);
        }
    }

    #[Route(path: '/{code}', name: 'app_api_country_delete', methods: ['DELETE'])]
    public function delete(string $code): JsonResponse {
        try {
            $this->countries->delete($code);
            return $this->json(data: null, status: 204);
        } catch (InvalidCodeException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'invalidCode' => $ex->invalidCode
            ], 400);
        } catch (CountryNotFoundException $ex) {
            return $this->json([
                'errorCode' => $ex->getCode(),
                'errorMessage' => $ex->getMessage(),
                'notFoundCode' => $ex->notFoundCode
            ], 404);
        }
    }
}