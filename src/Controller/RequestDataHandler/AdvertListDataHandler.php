<?php

namespace App\Controller\RequestDataHandler;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AdvertListDataHandler
{
    private ?string $categoryPropertyPrefix;
    private ?string $subCategoryPropertyPrefix;
    private ?string $typePropertyPrefix;
    private ?string $cityPropertyPrefix;
    private ?string $urgentPropertyPrefix;
    private ?string $marquePropertyPrefix;
    private ?string $modelPropertyPrefix;
    private ?string $typeCarburantPropertyPrefix;
    private ?string $autoStatePropertyPrefix;
    private ?string $nombrePiecePropertyPrefix;
    private ?string $nombreChambrePropertyPrefix;
    private ?string $nombreSalleBainPropertyPrefix;
    private ?string $immobilierStatePropertyPrefix;
    private ?string $brandPropertyPrefix;
    private ?string $statePropertyPrefix;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->categoryPropertyPrefix = $parameterBag->get('app_category_property_prefix');
        $this->subCategoryPropertyPrefix = $parameterBag->get('app_sub_category_property_prefix');
        $this->typePropertyPrefix = $parameterBag->get('app_type_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
        $this->urgentPropertyPrefix = $parameterBag->get('app_urgent_property_prefix');
        $this->marquePropertyPrefix = $parameterBag->get('app_marque_property_prefix');
        $this->modelPropertyPrefix = $parameterBag->get('app_model_property_prefix');
        $this->typeCarburantPropertyPrefix = $parameterBag->get('app_type_carburant_property_prefix');
        $this->autoStatePropertyPrefix = $parameterBag->get('app_auto_state_property_prefix');

        $this->nombrePiecePropertyPrefix = $parameterBag->get('app_nombre_piece_property_prefix');
        $this->nombreChambrePropertyPrefix = $parameterBag->get('app_nombre_chambre_property_prefix');
        $this->nombreSalleBainPropertyPrefix = $parameterBag->get('app_nombre_salle_bain_property_prefix');
        $this->immobilierStatePropertyPrefix = $parameterBag->get('app_immobilier_state_property_prefix');

        $this->brandPropertyPrefix = $parameterBag->get('app_brand_property_prefix');
        $this->statePropertyPrefix = $parameterBag->get('app_state_property_prefix');
    }

    public function retrieveData(array $requestData): array
    {
        $data[$this->categoryPropertyPrefix] = (string) $requestData[$this->categoryPropertyPrefix];
        $data[$this->subCategoryPropertyPrefix] = (string) $requestData[$this->subCategoryPropertyPrefix];
        $data[$this->typePropertyPrefix] = $requestData[$this->typePropertyPrefix];
        $data[$this->cityPropertyPrefix] = (string) $requestData[$this->cityPropertyPrefix];
        $data[$this->urgentPropertyPrefix] = (bool) $requestData[$this->urgentPropertyPrefix];

        //$data[$this->marquePropertyPrefix] = (string) $requestData[$this->marquePropertyPrefix];
        /*$data[$this->modelPropertyPrefix] = (string) $requestData[$this->modelPropertyPrefix];
        $data[$this->typeCarburantPropertyPrefix] = (string) $requestData[$this->typeCarburantPropertyPrefix];
        $data[$this->autoStatePropertyPrefix] = (string) $requestData[$this->autoStatePropertyPrefix];*/
        $this->handleMarquePrefixedProperty($requestData, $data);
        $this->handleModelPrefixedProperty($requestData, $data);
        $this->handleTypeCarburantPrefixedProperty($requestData, $data);
        $this->handleAutoStatePrefixedProperty($requestData, $data);
        $this->handleAutoYearPrefixedProperty($requestData, $data);
        $this->handleKiloPrefixedProperty($requestData, $data);
        $this->handleSurfacePrefixedProperty($requestData, $data);

        $this->handleNombrePiecePrefixedProperty($requestData, $data);
        $this->handleNombreChambrePrefixedProperty($requestData, $data);
        $this->handleNombreSalleBainPrefixedProperty($requestData, $data);
        $this->handleImmobilierStatePrefixedProperty($requestData, $data);
        $this->handleAccessPrefixedProperty($requestData, $data);
        $this->handleInteriorPrefixedProperty($requestData, $data);
        $this->handleExteriorPrefixedProperty($requestData, $data);
        $this->handleProximitePrefixedProperty($requestData, $data);

        $this->handleBrandPrefixedProperty($requestData, $data);
        $this->handleStatePrefixedProperty($requestData, $data);
        $this->handleProcessingPrefixedProperty($requestData, $data);

        $data = array_merge($data, $requestData['price']);



        /*$this->handleCategoryPrefixedProperty($requestData, $data);
        $this->handleSubCategoryPrefixedProperty($requestData, $data);
        $this->handleTypePrefixedProperty($requestData, $data);*/

        return $data;
    }

    private function handleMarquePrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->marquePropertyPrefix])) {
            return;
        }

        $data[$this->marquePropertyPrefix] = (string) $requestData[$this->marquePropertyPrefix];
    }

    private function handleModelPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->modelPropertyPrefix])) {
            return;
        }

        $data[$this->modelPropertyPrefix] = (string) $requestData[$this->modelPropertyPrefix];
    }

    private function handleTypeCarburantPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->typeCarburantPropertyPrefix])) {
            return;
        }

        $data[$this->typeCarburantPropertyPrefix] = (string) $requestData[$this->typeCarburantPropertyPrefix];
    }

    private function handleAutoStatePrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->autoStatePropertyPrefix])) {
            return;
        }

        $data[$this->autoStatePropertyPrefix] = (string) $requestData[$this->autoStatePropertyPrefix];
    }

    private function handleAutoYearPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['autoYear'])) {
            return;
        }

        $data = array_merge($data, $requestData['autoYear']);
    }

    private function handleKiloPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['kilo'])) {
            return;
        }

        $data = array_merge($data, $requestData['kilo']);
    }

    private function handleSurfacePrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['surface'])) {
            return;
        }

        $data = array_merge($data, $requestData['surface']);
    }

    private function handleNombrePiecePrefixedProperty(array $requestData, array &$data)
    {
        if (!isset($requestData[$this->nombrePiecePropertyPrefix])) {
            return;
        }

        $data[$this->nombrePiecePropertyPrefix] = (string) $requestData[$this->nombrePiecePropertyPrefix];
    }

    private function handleNombreChambrePrefixedProperty(array $requestData, array &$data)
    {
        if (!isset($requestData[$this->nombreChambrePropertyPrefix])) {
            return;
        }

        $data[$this->nombreChambrePropertyPrefix] = (string) $requestData[$this->nombreChambrePropertyPrefix];
    }

    private function handleNombreSalleBainPrefixedProperty(array $requestData, array &$data)
    {
        if (!isset($requestData[$this->nombreSalleBainPropertyPrefix])) {
            return;
        }

        $data[$this->nombreSalleBainPropertyPrefix] = (string) $requestData[$this->nombreSalleBainPropertyPrefix];
    }

    private function handleImmobilierStatePrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->immobilierStatePropertyPrefix])) {
            return;
        }

        $data[$this->immobilierStatePropertyPrefix] = (string) $requestData[$this->immobilierStatePropertyPrefix];
    }

    private function handleAccessPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['access'])) {
            return;
        }

        $data['access'] = $requestData['access'];
    }

    private function handleInteriorPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['interior'])) {
            return;
        }

        $data['interior'] = $requestData['interior'];
    }

    private function handleExteriorPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['exterior'])) {
            return;
        }

        $data['exterior'] = $requestData['exterior'];
    }

    private function handleProximitePrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['proximite'])) {
            return;
        }

        $data['proximite'] = $requestData['proximite'];
    }

    private function handleBrandPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->brandPropertyPrefix])) {
            return;
        }

        $data[$this->brandPropertyPrefix] = (string) $requestData[$this->brandPropertyPrefix];
    }

    private function handleStatePrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData[$this->statePropertyPrefix])) {
            return;
        }

        $data[$this->statePropertyPrefix] = (string) $requestData[$this->statePropertyPrefix];
    }

    private function handleProcessingPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['processing'])) {
            return;
        }

        $data['processing'] = $requestData['processing'];
    }










    private function handleEquipmentsPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['equipments'])) {
            return;
        }

        if (array_key_exists('equipments', $requestData['equipments'])) {
            $data['equipments'] = $requestData['equipments']['equipments'];
        } else {
            $data['equipments'] = $requestData['equipments'];
        }
    }

    private function handleRoomEquipmentsPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['roomEquipments'])) {
            return;
        }

        if (array_key_exists('roomEquipments', $requestData['roomEquipments'])) {
            $data['roomEquipments'] = $requestData['roomEquipments']['roomEquipments'];
        } else {
            $data['roomEquipments'] = $requestData['roomEquipments'];
        }
    }

    private function handleCategoryPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['category'])) {
            return;
        }

        $data['category'] = $requestData['category'];
    }

    private function handleSubCategoryPrefixedProperty(array $requestData, array &$data): void
    {
        if (!isset($requestData['subCategory'])) {
            return;
        }

        $data['subCategory'] = $requestData['subCategory'];
    }

    private function handleTypePrefixedProperty(array $requestData, array &$data)
    {
        if (!isset($requestData['type'])) {
            return;
        }

        $data['type'] = $requestData['type'];
    }
}
