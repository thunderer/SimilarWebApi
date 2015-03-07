<?php
/**
 * THIS FILE WAS GENERATED AUTOMATICALLY. ALL CHANGES WILL BE OVERWRITTEN DURING
 * COMPOSER INSTALL, COMPOSER UPDATE OR MANUAL EXECUTION OF BIN/GENERATE SCRIPT.
 */

namespace Thunder\SimilarWebApi;

use Thunder\SimilarWebApi\Request\GlobalRank as GlobalRankRequest;
use Thunder\SimilarWebApi\Response\GlobalRank as GlobalRankResponse;
use Thunder\SimilarWebApi\Request\SimilarSites as SimilarSitesRequest;
use Thunder\SimilarWebApi\Response\SimilarSites as SimilarSitesResponse;
use Thunder\SimilarWebApi\Request\Tagging as TaggingRequest;
use Thunder\SimilarWebApi\Response\Tagging as TaggingResponse;
use Thunder\SimilarWebApi\Request\V0WebsiteCategorization as V0WebsiteCategorizationRequest;
use Thunder\SimilarWebApi\Response\V0WebsiteCategorization as V0WebsiteCategorizationResponse;
use Thunder\SimilarWebApi\Request\WebsiteCategoryRank as WebsiteCategoryRankRequest;
use Thunder\SimilarWebApi\Response\WebsiteCategoryRank as WebsiteCategoryRankResponse;
use Thunder\SimilarWebApi\Request\WebsiteCountryRank as WebsiteCountryRankRequest;
use Thunder\SimilarWebApi\Response\WebsiteCountryRank as WebsiteCountryRankResponse;
use Thunder\SimilarWebApi\Request\Traffic as TrafficRequest;
use Thunder\SimilarWebApi\Response\Traffic as TrafficResponse;
use Thunder\SimilarWebApi\Request\Engagement as EngagementRequest;
use Thunder\SimilarWebApi\Response\Engagement as EngagementResponse;
use Thunder\SimilarWebApi\Request\Keywords as KeywordsRequest;
use Thunder\SimilarWebApi\Response\Keywords as KeywordsResponse;
use Thunder\SimilarWebApi\Request\SocialReferrals as SocialReferralsRequest;
use Thunder\SimilarWebApi\Response\SocialReferrals as SocialReferralsResponse;
use Thunder\SimilarWebApi\Request\AdultWebsites as AdultWebsitesRequest;
use Thunder\SimilarWebApi\Response\AdultWebsites as AdultWebsitesResponse;
use Thunder\SimilarWebApi\Request\AlsoVisited as AlsoVisitedRequest;
use Thunder\SimilarWebApi\Response\AlsoVisited as AlsoVisitedResponse;
use Thunder\SimilarWebApi\Request\CategoryRank as CategoryRankRequest;
use Thunder\SimilarWebApi\Response\CategoryRank as CategoryRankResponse;
use Thunder\SimilarWebApi\Request\Destinations as DestinationsRequest;
use Thunder\SimilarWebApi\Response\Destinations as DestinationsResponse;
use Thunder\SimilarWebApi\Request\EstimatedVisitors as EstimatedVisitorsRequest;
use Thunder\SimilarWebApi\Response\EstimatedVisitors as EstimatedVisitorsResponse;
use Thunder\SimilarWebApi\Request\Referrals as ReferralsRequest;
use Thunder\SimilarWebApi\Response\Referrals as ReferralsResponse;
use Thunder\SimilarWebApi\Request\SimilarWebsites as SimilarWebsitesRequest;
use Thunder\SimilarWebApi\Response\SimilarWebsites as SimilarWebsitesResponse;
use Thunder\SimilarWebApi\Request\WebsiteCategorization as WebsiteCategorizationRequest;
use Thunder\SimilarWebApi\Response\WebsiteCategorization as WebsiteCategorizationResponse;
use Thunder\SimilarWebApi\Request\WebsiteTags as WebsiteTagsRequest;
use Thunder\SimilarWebApi\Response\WebsiteTags as WebsiteTagsResponse;
use Thunder\SimilarWebApi\Request\TrafficPro as TrafficProRequest;
use Thunder\SimilarWebApi\Response\TrafficPro as TrafficProResponse;
use Thunder\SimilarWebApi\Request\EngagementPageViews as EngagementPageViewsRequest;
use Thunder\SimilarWebApi\Response\EngagementPageViews as EngagementPageViewsResponse;
use Thunder\SimilarWebApi\Request\EngagementVisitDuration as EngagementVisitDurationRequest;
use Thunder\SimilarWebApi\Response\EngagementVisitDuration as EngagementVisitDurationResponse;
use Thunder\SimilarWebApi\Request\EngagementBounceRate as EngagementBounceRateRequest;
use Thunder\SimilarWebApi\Response\EngagementBounceRate as EngagementBounceRateResponse;
use Thunder\SimilarWebApi\Request\KeywordsOrganicSearch as KeywordsOrganicSearchRequest;
use Thunder\SimilarWebApi\Response\KeywordsOrganicSearch as KeywordsOrganicSearchResponse;
use Thunder\SimilarWebApi\Request\KeywordsPaidSearch as KeywordsPaidSearchRequest;
use Thunder\SimilarWebApi\Response\KeywordsPaidSearch as KeywordsPaidSearchResponse;
use Thunder\SimilarWebApi\Request\ReferralsPro as ReferralsProRequest;
use Thunder\SimilarWebApi\Response\ReferralsPro as ReferralsProResponse;
use Thunder\SimilarWebApi\Request\KeywordCompetitorsOrganic as KeywordCompetitorsOrganicRequest;
use Thunder\SimilarWebApi\Response\KeywordCompetitorsOrganic as KeywordCompetitorsOrganicResponse;
use Thunder\SimilarWebApi\Request\KeywordCompetitorsPaid as KeywordCompetitorsPaidRequest;
use Thunder\SimilarWebApi\Response\KeywordCompetitorsPaid as KeywordCompetitorsPaidResponse;
use Thunder\SimilarWebApi\Request\MobileApp as MobileAppRequest;
use Thunder\SimilarWebApi\Response\MobileApp as MobileAppResponse;
use Thunder\SimilarWebApi\Request\MobileAppInstalls as MobileAppInstallsRequest;
use Thunder\SimilarWebApi\Response\MobileAppInstalls as MobileAppInstallsResponse;
use Thunder\SimilarWebApi\Request\MobileRelatedApps as MobileRelatedAppsRequest;
use Thunder\SimilarWebApi\Response\MobileRelatedApps as MobileRelatedAppsResponse;

/**
 * @author Tomasz Kowalczyk <tomasz@kowalczyk.cc>
 */
final class ClientFacade
    {
    private $client;

    public function __construct(Client $client)
        {
        $this->client = $client;
        }

    /**
     * @var $domain
     *
     * @return GlobalRankResponse
     */
    public function getGlobalRankResponse($domain)
        {
        return $this->client->getResponse(new GlobalRankRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return SimilarSitesResponse
     */
    public function getSimilarSitesResponse($domain)
        {
        return $this->client->getResponse(new SimilarSitesRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return TaggingResponse
     */
    public function getTaggingResponse($domain)
        {
        return $this->client->getResponse(new TaggingRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return V0WebsiteCategorizationResponse
     */
    public function getV0WebsiteCategorizationResponse($domain)
        {
        return $this->client->getResponse(new V0WebsiteCategorizationRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return WebsiteCategoryRankResponse
     */
    public function getWebsiteCategoryRankResponse($domain)
        {
        return $this->client->getResponse(new WebsiteCategoryRankRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return WebsiteCountryRankResponse
     */
    public function getWebsiteCountryRankResponse($domain)
        {
        return $this->client->getResponse(new WebsiteCountryRankRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return TrafficResponse
     */
    public function getTrafficResponse($domain)
        {
        return $this->client->getResponse(new TrafficRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return EngagementResponse
     */
    public function getEngagementResponse($domain)
        {
        return $this->client->getResponse(new EngagementRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return KeywordsResponse
     */
    public function getKeywordsResponse($domain)
        {
        return $this->client->getResponse(new KeywordsRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return SocialReferralsResponse
     */
    public function getSocialReferralsResponse($domain)
        {
        return $this->client->getResponse(new SocialReferralsRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return AdultWebsitesResponse
     */
    public function getAdultWebsitesResponse($domain)
        {
        return $this->client->getResponse(new AdultWebsitesRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return AlsoVisitedResponse
     */
    public function getAlsoVisitedResponse($domain)
        {
        return $this->client->getResponse(new AlsoVisitedRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return CategoryRankResponse
     */
    public function getCategoryRankResponse($domain)
        {
        return $this->client->getResponse(new CategoryRankRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return DestinationsResponse
     */
    public function getDestinationsResponse($domain)
        {
        return $this->client->getResponse(new DestinationsRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return EstimatedVisitorsResponse
     */
    public function getEstimatedVisitorsResponse($domain)
        {
        return $this->client->getResponse(new EstimatedVisitorsRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return ReferralsResponse
     */
    public function getReferralsResponse($domain)
        {
        return $this->client->getResponse(new ReferralsRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return SimilarWebsitesResponse
     */
    public function getSimilarWebsitesResponse($domain)
        {
        return $this->client->getResponse(new SimilarWebsitesRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return WebsiteCategorizationResponse
     */
    public function getWebsiteCategorizationResponse($domain)
        {
        return $this->client->getResponse(new WebsiteCategorizationRequest($domain));
        }

    /**
     * @var $domain
     *
     * @return WebsiteTagsResponse
     */
    public function getWebsiteTagsResponse($domain)
        {
        return $this->client->getResponse(new WebsiteTagsRequest($domain));
        }

    /**
     * @var $domain
     * @var $period
     * @var $start
     * @var $end
     * @var $main
     *
     * @return TrafficProResponse
     */
    public function getTrafficProResponse($domain, $period, $start, $end, $main)
        {
        return $this->client->getResponse(new TrafficProRequest($domain, $period, $start, $end, $main));
        }

    /**
     * @var $domain
     * @var $period
     * @var $start
     * @var $end
     * @var $main
     *
     * @return EngagementPageViewsResponse
     */
    public function getEngagementPageViewsResponse($domain, $period, $start, $end, $main)
        {
        return $this->client->getResponse(new EngagementPageViewsRequest($domain, $period, $start, $end, $main));
        }

    /**
     * @var $domain
     * @var $period
     * @var $start
     * @var $end
     * @var $main
     *
     * @return EngagementVisitDurationResponse
     */
    public function getEngagementVisitDurationResponse($domain, $period, $start, $end, $main)
        {
        return $this->client->getResponse(new EngagementVisitDurationRequest($domain, $period, $start, $end, $main));
        }

    /**
     * @var $domain
     * @var $period
     * @var $start
     * @var $end
     * @var $main
     *
     * @return EngagementBounceRateResponse
     */
    public function getEngagementBounceRateResponse($domain, $period, $start, $end, $main)
        {
        return $this->client->getResponse(new EngagementBounceRateRequest($domain, $period, $start, $end, $main));
        }

    /**
     * @var $domain
     * @var $start
     * @var $end
     * @var $main
     * @var $page
     *
     * @return KeywordsOrganicSearchResponse
     */
    public function getKeywordsOrganicSearchResponse($domain, $start, $end, $main, $page)
        {
        return $this->client->getResponse(new KeywordsOrganicSearchRequest($domain, $start, $end, $main, $page));
        }

    /**
     * @var $domain
     * @var $start
     * @var $end
     * @var $main
     * @var $page
     *
     * @return KeywordsPaidSearchResponse
     */
    public function getKeywordsPaidSearchResponse($domain, $start, $end, $main, $page)
        {
        return $this->client->getResponse(new KeywordsPaidSearchRequest($domain, $start, $end, $main, $page));
        }

    /**
     * @var $domain
     * @var $start
     * @var $end
     * @var $main
     * @var $page
     *
     * @return ReferralsProResponse
     */
    public function getReferralsProResponse($domain, $start, $end, $main, $page)
        {
        return $this->client->getResponse(new ReferralsProRequest($domain, $start, $end, $main, $page));
        }

    /**
     * @var $domain
     * @var $start
     * @var $end
     * @var $main
     * @var $page
     *
     * @return KeywordCompetitorsOrganicResponse
     */
    public function getKeywordCompetitorsOrganicResponse($domain, $start, $end, $main, $page)
        {
        return $this->client->getResponse(new KeywordCompetitorsOrganicRequest($domain, $start, $end, $main, $page));
        }

    /**
     * @var $domain
     * @var $start
     * @var $end
     * @var $main
     * @var $page
     *
     * @return KeywordCompetitorsPaidResponse
     */
    public function getKeywordCompetitorsPaidResponse($domain, $start, $end, $main, $page)
        {
        return $this->client->getResponse(new KeywordCompetitorsPaidRequest($domain, $start, $end, $main, $page));
        }

    /**
     * @var $store
     * @var $app
     *
     * @return MobileAppResponse
     */
    public function getMobileAppResponse($store, $app)
        {
        return $this->client->getResponse(new MobileAppRequest($store, $app));
        }

    /**
     * @var $store
     * @var $app
     *
     * @return MobileAppInstallsResponse
     */
    public function getMobileAppInstallsResponse($store, $app)
        {
        return $this->client->getResponse(new MobileAppInstallsRequest($store, $app));
        }

    /**
     * @var $store
     * @var $domain
     *
     * @return MobileRelatedAppsResponse
     */
    public function getMobileRelatedAppsResponse($store, $domain)
        {
        return $this->client->getResponse(new MobileRelatedAppsRequest($store, $domain));
        }
    }
