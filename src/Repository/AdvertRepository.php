<?php

namespace App\Repository;

use App\Controller\RequestDataHandler\AdvertSortDataHandler;
use App\Entity\Advert;
use App\Entity\User;
use App\Manager\AdvertManager;
use App\Model\Admin\AdvertSearch;
use App\PropertyNameResolver\KiloNameResolver;
use App\PropertyNameResolver\PriceNameResolver;
use App\PropertyNameResolver\SurfaceNameResolver;
use App\PropertyNameResolver\YearNameResolver;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @extends ServiceEntityRepository<Advert>
 *
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    private ?string $categoryPropertyPrefix;
    private ?string $subCategoryPropertyPrefix;
    private ?string $typePropertyPrefix;
    private ?string $cityPropertyPrefix;
    private ?string $urgentPropertyPrefix;
    private ?string $dataPropertyPrefix;

    private ?string $marquePropertyPrefix;
    private ?string $modelPropertyPrefix;
    private ?string $typeCarburantPropertyPrefix;
    private ?string $autoStatePropertyPrefix;
    private ?string $nombrePiecePropertyPrefix;
    private ?string $nombreChambrePropertyPrefix;
    private ?string $nombreSalleBainPropertyPrefix;
    private ?string $immobilierStatePropertyPrefix;
    private ?string $proximitePropertyPrefix;
    private ?string $exteriorPropertyPrefix;
    private ?string $interiorPropertyPrefix;
    private ?string $accessPropertyPrefix;
    private ?string $brandPropertyPrefix;
    private ?string $statePropertyPrefix;
    private ?string $processingPropertyPrefix;

    public function __construct(
        ManagerRegistry $registry,
        ParameterBagInterface $parameterBag,
        private AdvertManager $manager,
        private PriceNameResolver $priceNameResolver,
        private YearNameResolver $yearNameResolver,
        private KiloNameResolver $kiloNameResolver,
        private SurfaceNameResolver $surfaceNameResolver
    )
    {
        parent::__construct($registry, Advert::class);

        $this->categoryPropertyPrefix = $parameterBag->get('app_category_property_prefix');
        $this->subCategoryPropertyPrefix = $parameterBag->get('app_sub_category_property_prefix');
        $this->typePropertyPrefix = $parameterBag->get('app_type_property_prefix');
        $this->cityPropertyPrefix = $parameterBag->get('app_city_property_prefix');
        $this->urgentPropertyPrefix = $parameterBag->get('app_urgent_property_prefix');
        $this->dataPropertyPrefix = $parameterBag->get('app_data_property_prefix');

        $this->marquePropertyPrefix = $parameterBag->get('app_marque_property_prefix');
        $this->modelPropertyPrefix = $parameterBag->get('app_model_property_prefix');
        $this->typeCarburantPropertyPrefix = $parameterBag->get('app_type_carburant_property_prefix');
        $this->autoStatePropertyPrefix = $parameterBag->get('app_auto_state_property_prefix');
        $this->nombrePiecePropertyPrefix = $parameterBag->get('app_nombre_piece_property_prefix');
        $this->nombreChambrePropertyPrefix = $parameterBag->get('app_nombre_chambre_property_prefix');
        $this->nombreSalleBainPropertyPrefix = $parameterBag->get('app_nombre_salle_bain_property_prefix');
        $this->immobilierStatePropertyPrefix = $parameterBag->get('app_immobilier_state_property_prefix');

        $this->proximitePropertyPrefix = $parameterBag->get('app_proximite_property_prefix');
        $this->exteriorPropertyPrefix = $parameterBag->get('app_exterior_property_prefix');
        $this->interiorPropertyPrefix = $parameterBag->get('app_interior_property_prefix');
        $this->accessPropertyPrefix = $parameterBag->get('app_access_property_prefix');

        $this->brandPropertyPrefix = $parameterBag->get('app_brand_property_prefix');
        $this->statePropertyPrefix = $parameterBag->get('app_state_property_prefix');
        $this->processingPropertyPrefix = $parameterBag->get('app_processing_property_prefix');
    }

    public function add(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Advert $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getAdmins(AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location');

        $qb->where($qb->expr()->isNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->orderBy('a.position', 'asc');

        return $this->adminSearchData($search, $qb);
    }

    public function getValidateAdmins(AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->setParameter('date', $this->manager->activatedDays())
            ->orderBy('a.validatedAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getDeniedAdmins(AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location');

        $qb->where($qb->expr()->isNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNotNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->orderBy('a.deletedAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getArchiveAdmins(AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt <= :date')
            ->setParameter('date', $this->manager->activatedDays())
            ->orderBy('a.validatedAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getRemoveAdmins(AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location');

        $qb->where($qb->expr()->isNotNull('a.deletedAt'))
            ->orderBy('a.deletedAt', 'desc');

        return $this->adminSearchData($search, $qb);
    }

    public function getAdminByUser(User $user, AdvertSearch $search): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->where('a.owner = :user')
            ->setParameter('user', $user);

        return $this->adminSearchData($search, $qb);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getValidatedByUserNumber(User $user): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->andWhere('a.owner = :owner')
            ->setParameter('date', $this->manager->activatedDays())
            ->setParameter('owner', $user);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function getValidatedByUser(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images');

        return $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->andWhere('a.owner = :owner')
            ->setParameter('date', $this->manager->activatedDays())
            ->setParameter('owner', $user);
    }


    public function getByUser(User $user): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images');

        return $qb->where($qb->expr()->isNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.owner = :owner')
            ->setParameter('owner', $user);
    }

    private function adminSearchData(AdvertSearch $search, QueryBuilder $qb): QueryBuilder
    {
        if ($search->getCategory()) {
            $qb->andWhere('a.category = :category')->setParameter('category', $search->getCategory());
        }

        if ($search->getSubCategory()) {
            $qb->andWhere('a.subCategory = :subCategory')->setParameter('subCategory', $search->getSubCategory());
        }

        if ($search->getAdvertType()) {
            $qb->andWhere('a.type = :type')->setParameter('type', $search->getAdvertType());
        }

        if ($search->getReference()) {
            $qb->andWhere('a.reference = :reference')->setParameter('reference', $search->getReference());
        }

        if ($search->getCity()) {
            $qb->andWhere('location.city = :city')->setParameter('city', $search->getCity());
        }

        return $qb;
    }

    public function getAdvertLists(array $data): QueryBuilder
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->leftJoin('a.favorites', 'favorites')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images')
            ->addSelect('favorites');

        $qb = $this->addFilterData($qb, $data);

        $qb->andWhere($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->setParameter('date', $this->manager->activatedDays());

        $qb = $this->addSortData($qb, $data);

        return $qb;
    }

    public function getAdvertHybrideLists(array $data): QueryBuilder
    {
        dump($data);
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->leftJoin('a.favorites', 'favorites')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images')
            ->addSelect('favorites');

        $qb = $this->hasDataQueryBuilder($qb, $data);
        $qb = $this->hasTypeQueryBuilder($qb, $data);
        $qb = $this->hasUrgentQueryBuilder($qb, $data);
        $qb = $this->hasCityQueryBuilder($qb, $data);
        $qb = $this->hasPriceBetweenQueryBuilder($qb, $data);

        $qb->andWhere($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->setParameter('date', $this->manager->activatedDays());

        $qb = $this->addSortData($qb, $data);

        return $qb;
    }

    private function addSortData(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[AdvertSortDataHandler::SORT_INDEX]) {
            $property = array_key_first($data[AdvertSortDataHandler::SORT_INDEX]);

            $qb->orderBy($property, $data[AdvertSortDataHandler::SORT_INDEX][$property]);
        }

        return $qb;
    }

    private function addFilterData(QueryBuilder $qb, array $data): QueryBuilder
    {
        $qb = $this->hasDataQueryBuilder($qb, $data);
        $qb = $this->hasCategoriesQueryBuilder($qb, $data);
        $qb = $this->hasSubCategoriesQueryBuilder($qb, $data);
        $qb = $this->hasTypeQueryBuilder($qb, $data);
        $qb = $this->hasUrgentQueryBuilder($qb, $data);
        $qb = $this->hasCityQueryBuilder($qb, $data);
        $qb = $this->hasPriceBetweenQueryBuilder($qb, $data);
        $qb = $this->hasAutoYearBetweenQueryBuilder($qb, $data);
        $qb = $this->hasKiloBetweenQueryBuilder($qb, $data);
        $qb = $this->hasSurfaceBetweenQueryBuilder($qb, $data);

        $qb = $this->hasMarqueQueryBuilder($qb, $data);
        $qb = $this->hasModelQueryBuilder($qb, $data);
        $qb = $this->hasTypeCarburantQueryBuilder($qb, $data);
        $qb = $this->hasAutoStateQueryBuilder($qb, $data);

        $qb = $this->hasNombrePieceQueryBuilder($qb, $data);
        $qb = $this->hasNombreChambreQueryBuilder($qb, $data);
        $qb = $this->hasNombreSalleBainQueryBuilder($qb, $data);
        $qb = $this->hasImmobilierStateQueryBuilder($qb, $data);

        $qb = $this->hasAccessQueryBuilder($qb, $data);
        $qb = $this->hasExteriorQueryBuilder($qb, $data);
        $qb = $this->hasInteriorQueryBuilder($qb, $data);
        $qb = $this->hasProximiteQueryBuilder($qb, $data);

        $qb = $this->hasBrandQueryBuilder($qb, $data);
        $qb = $this->hasStateQueryBuilder($qb, $data);
        $qb = $this->hasProcessingQueryBuilder($qb, $data);

        return $qb;
    }

    private function hasCategoriesQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->categoryPropertyPrefix]) {
            $qb->andWhere('category.slug = :category')->setParameter('category', $data[$this->categoryPropertyPrefix]);
        }

        return $qb;
    }

    private function hasSubCategoriesQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->subCategoryPropertyPrefix]) {
            $qb->andWhere('subCategory.slug = :subCategory')->setParameter('subCategory', $data[$this->subCategoryPropertyPrefix]);
        }

        return $qb;
    }

    private function hasTypeQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->typePropertyPrefix]) {
            $qb->andWhere('a.type = :type')->setParameter('type', $data[$this->typePropertyPrefix]);
        }

        return $qb;
    }

    private function hasUrgentQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->urgentPropertyPrefix]) {
            $qb->andWhere('a.optionAdvertUrgentEndAt > NOW()');
        }

        return $qb;
    }

    private function hasCityQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if ($data[$this->cityPropertyPrefix]) {
            $qb->andWhere('location.city = :city')->setParameter('city', $data[$this->cityPropertyPrefix]);
        }

        return $qb;
    }

    private function hasPriceBetweenQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        $minPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMinPriceName());
        $maxPrice = $this->getDataByKey($data, $this->priceNameResolver->resolveMaxPriceName());

        if ($minPrice) {
            $qb->andWhere($qb->expr()->gte('a.price', ':minPrice'))->setParameter('minPrice', (int)$minPrice);
        }

        if ($maxPrice) {
            $qb->andWhere($qb->expr()->lte('a.price', ':maxPrice'))->setParameter('maxPrice', (int)$maxPrice);
        }

        return $qb;
    }

    private function hasAutoYearBetweenQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        $minYear = $this->getDataByKey($data, $this->yearNameResolver->resolveMinYearName());
        $maxYear = $this->getDataByKey($data, $this->yearNameResolver->resolveMaxYearName());

        if ($minYear) {
            $qb->andWhere($qb->expr()->gte('a.autoYear', ':minYear'))->setParameter('minYear', $minYear);
        }

        if ($maxYear) {
            $qb->andWhere($qb->expr()->lte('a.autoYear', ':maxYear'))->setParameter('maxYear', $maxYear);
        }

        return $qb;
    }

    private function hasKiloBetweenQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        $minKilo = $this->getDataByKey($data, $this->kiloNameResolver->resolveMinKiloName());
        $maxKilo = $this->getDataByKey($data, $this->kiloNameResolver->resolveMaxKiloName());

        if ($minKilo) {
            $qb->andWhere($qb->expr()->gte('a.kilometrage', ':minKilo'))->setParameter('minKilo', (int)$minKilo);
        }

        if ($maxKilo) {
            $qb->andWhere($qb->expr()->lte('a.kilometrage', ':maxKilo'))->setParameter('maxKilo', (int)$maxKilo);
        }

        return $qb;
    }

    private function hasMarqueQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->marquePropertyPrefix, $data) &&
            $data[$this->marquePropertyPrefix]) {
            $qb->andWhere('a.marque = :marque')->setParameter('marque', $data[$this->marquePropertyPrefix]);
        }

        return $qb;
    }

    private function hasModelQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->modelPropertyPrefix, $data) &&
            $data[$this->modelPropertyPrefix]) {
            $qb->andWhere('a.model = :model')->setParameter('model', $data[$this->modelPropertyPrefix]);
        }

        return $qb;
    }

    private function hasTypeCarburantQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->typeCarburantPropertyPrefix, $data) &&
            $data[$this->typeCarburantPropertyPrefix]) {
            $qb->andWhere('a.typeCarburant = :typeCarburant')->setParameter('typeCarburant', $data[$this->typeCarburantPropertyPrefix]);
        }

        return $qb;
    }

    private function hasAutoStateQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->autoStatePropertyPrefix, $data) &&
            $data[$this->autoStatePropertyPrefix]) {
            $qb->andWhere('a.autoState = :autoState')->setParameter('autoState', $data[$this->autoStatePropertyPrefix]);
        }

        return $qb;
    }

    private function hasSurfaceBetweenQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        $minSurface = $this->getDataByKey($data, $this->surfaceNameResolver->resolveMinSurfaceName());
        $maxSurface = $this->getDataByKey($data, $this->surfaceNameResolver->resolveMaxSurfaceName());

        if ($minSurface) {
            $qb->andWhere($qb->expr()->gte('a.surface', ':minSurface'))->setParameter('minSurface', (int)$minSurface);
        }

        if ($maxSurface) {
            $qb->andWhere($qb->expr()->lte('a.surface', ':maxSurface'))->setParameter('maxSurface', (int)$maxSurface);
        }

        return $qb;
    }

    private function hasNombrePieceQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->nombrePiecePropertyPrefix, $data) &&
            $data[$this->nombrePiecePropertyPrefix]) {
            $qb->andWhere('a.nombrePiece = :nombrePiece')->setParameter('nombrePiece', $data[$this->nombrePiecePropertyPrefix]);
        }

        return $qb;
    }

    private function hasNombreChambreQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->nombreChambrePropertyPrefix, $data) &&
            $data[$this->nombreChambrePropertyPrefix]) {
            $qb->andWhere('a.nombreChambre = :nombreChambre')->setParameter('nombreChambre', $data[$this->nombreChambrePropertyPrefix]);
        }

        return $qb;
    }

    private function hasNombreSalleBainQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->nombreSalleBainPropertyPrefix, $data) &&
            $data[$this->nombreSalleBainPropertyPrefix]) {
            $qb->andWhere('a.nombreSalleBain = :nombreSalleBain')->setParameter('nombreSalleBain', $data[$this->nombreSalleBainPropertyPrefix]);
        }

        return $qb;
    }

    private function hasImmobilierStateQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->immobilierStatePropertyPrefix, $data) &&
            $data[$this->immobilierStatePropertyPrefix]) {
            $qb->andWhere('a.immobilierState = :immobilierState')->setParameter('immobilierState', $data[$this->immobilierStatePropertyPrefix]);
        }

        return $qb;
    }

    private function hasAccessQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->accessPropertyPrefix, $data) && $data[$this->accessPropertyPrefix]) {
            $qb->andWhere('a.access IN (:access)')->setParameter('access', $data[$this->accessPropertyPrefix]);
        }

        return $qb;
    }

    private function hasInteriorQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->interiorPropertyPrefix, $data) && $data[$this->interiorPropertyPrefix]) {
            $qb->andWhere('a.interior IN (:interior)')->setParameter('interior', $data[$this->interiorPropertyPrefix]);
        }

        return $qb;
    }

    private function hasExteriorQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->exteriorPropertyPrefix, $data) && $data[$this->exteriorPropertyPrefix]) {
            $qb->andWhere('a.exterior IN (:interior)')->setParameter('exterior', $data[$this->exteriorPropertyPrefix]);
        }

        return $qb;
    }

    private function hasProximiteQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->proximitePropertyPrefix, $data) && $data[$this->proximitePropertyPrefix]) {
            $qb->andWhere('a.proximite IN (:proximite)')->setParameter('proximite', $data[$this->proximitePropertyPrefix]);
        }

        return $qb;
    }

    private function hasBrandQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->brandPropertyPrefix, $data) &&
            $data[$this->brandPropertyPrefix]) {
            $qb->andWhere('a.brand = :brand')->setParameter('brand', $data[$this->brandPropertyPrefix]);
        }

        return $qb;
    }

    private function hasStateQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->statePropertyPrefix, $data) &&
            $data[$this->statePropertyPrefix]) {
            $qb->andWhere('a.state = :state')->setParameter('state', $data[$this->statePropertyPrefix]);
        }

        return $qb;
    }

    private function hasProcessingQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->processingPropertyPrefix, $data) && $data[$this->processingPropertyPrefix]) {
            $qb->andWhere('a.processing IN (:processing)')->setParameter('processing', $data[$this->processingPropertyPrefix]);
        }

        return $qb;
    }

    private function hasDataQueryBuilder(QueryBuilder $qb, array $data): QueryBuilder
    {
        if (array_key_exists($this->dataPropertyPrefix, $data) && $data[$this->dataPropertyPrefix]) {
            $qb->andWhere('a.title LIKE :data')
                ->orWhere('a.description LIKE :data')
                ->orWhere('a.marque LIKE :data')
                ->orWhere('a.model LIKE :data')
                ->orWhere('a.brand LIKE :data')
                ->setParameter('data', '%' . $data[$this->dataPropertyPrefix] . '%');
        }

        return $qb;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getEnabledBySlug(string $slug): ?Advert
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->leftJoin('a.favorites', 'favorites')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images')
            ->addSelect('favorites');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->andWhere('a.slug = :slug')
            ->setParameter('date', $this->manager->activatedDays())
            ->setParameter('slug', $slug);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getEnabledByById(int $id): ?Advert
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->andWhere('a.id = :id')
            ->setParameter('date', $this->manager->activatedDays())
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getSimilar(Advert $advert)
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->leftJoin('a.favorites', 'favorites')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images')
            ->addSelect('favorites');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->andWhere('a.category = :category')
            ->andWhere('a.type = :type')
            ->andWhere('location.city = :city')
            ->setParameter('date', $this->manager->activatedDays())
            ->setParameter('category', $advert->getCategory())
            ->setParameter('type', $advert->getType())
            ->setParameter('city', $advert->getLocation()->getCity());

        if ($advert->getSubCategory()) {
            $qb->andWhere('a.subCategory = :subCategory')
                ->setParameter('subCategory', $advert->getSubCategory());
        }

        $qb->orderBy('a.validatedAt', 'desc')
            ->setMaxResults(3);

        return $qb->getQuery()->getResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function newNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        $qb->where($qb->expr()->isNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function validatedNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->setParameter('date', $this->manager->activatedDays());

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function deniedNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        $qb->where($qb->expr()->isNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNotNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function archiveNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        $qb->where($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt <= :date')
            ->setParameter('date', $this->manager->activatedDays());

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function removeNumber(): int
    {
        $qb = $this->createQueryBuilder('a')
            ->select('count(a.id)');

        $qb->where($qb->expr()->isNotNull('a.deletedAt'));

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function searchByQuery(string $query): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.category', 'category')
            ->leftJoin('a.subCategory', 'subCategory')
            ->leftJoin('a.location', 'location')
            ->leftJoin('a.images', 'images')
            ->addSelect('category')
            ->addSelect('subCategory')
            ->addSelect('location')
            ->addSelect('images')
            ->where('a.title LIKE :query')
            ->orWhere('a.description LIKE :query')
            ->orWhere('category.name LIKE :query')
            ->orWhere('subCategory.name LIKE :query')
            ->orWhere('a.marque LIKE :query')
            ->orWhere('a.model LIKE :query')
            ->orWhere('a.brand LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        $qb->andWhere($qb->expr()->isNotNull('a.validatedAt'))
            ->andWhere($qb->expr()->isNull('a.deniedAt'))
            ->andWhere($qb->expr()->isNull('a.deletedAt'))
            ->andWhere('a.validatedAt >= :date')
            ->setParameter('date', $this->manager->activatedDays())
            ->orderBy('a.position', 'desc')
            ->setMaxResults(8);

        return $qb->getQuery()->getResult();
    }














    private function getDataByKey(array $data, string $key): ?string
    {
        return $data[$key] ?? null;
    }
}
