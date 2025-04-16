import ComingSoon from '../Components/ComingSoon.vue';
import Services from '../Components/Services.vue';
import CreateServiceRequest from '../Pages/CreateServiceRequest.vue';
import Home from '../Pages/Home.vue';
import Licenses from '../Pages/Licenses.vue';
import SelectServiceRequestType from '../Pages/SelectServiceRequestType.vue';
import ViewArticle from '../Pages/ViewArticle.vue';
import ViewCategory from '../Pages/ViewCategory.vue';
import ViewServiceRequest from '../Pages/ViewServiceRequest.vue';
import getAppContext from '../Services/GetAppContext.js';

const { isEmbeddedInAidingApp, baseUrl } = getAppContext(window.portalConfig.accessUrl);

console.log('baseUrl:', baseUrl);

export default [
    {
        path: baseUrl + '/',
        name: 'home',
        component: Home,
    },
    {
        path: baseUrl + '/categories/:categorySlug',
        name: 'view-category',
        component: ViewCategory,
    },
    {
        path: baseUrl + '/categories/:parentCategorySlug/:categorySlug',
        name: 'view-subcategory',
        component: ViewCategory,
    },
    {
        path: baseUrl + '/categories/:categorySlug/articles/:articleId',
        name: 'view-article',
        component: ViewArticle,
    },
    {
        path: baseUrl + '/service-request-type/select',
        name: 'select-service-request',
        component: SelectServiceRequestType,
    },
    {
        path: baseUrl + '/service-request/create/:typeId',
        name: 'create-service-request',
        component: CreateServiceRequest,
    },
    {
        path: baseUrl + '/service-request/:serviceRequestId',
        name: 'view-service-request',
        component: ViewServiceRequest,
    },
    {
        path: baseUrl + '/services',
        name: 'services',
        component: Services,
    },
    {
        path: baseUrl + '/incidents',
        name: 'incidents',
        component: ComingSoon,
    },
    {
        path: baseUrl + '/knowledge-base',
        name: 'knowledge-base',
        component: ComingSoon,
    },
    {
        path: baseUrl + '/assets',
        name: 'assets',
        component: ComingSoon,
    },
    {
        path: baseUrl + '/licenses',
        name: 'licenses',
        component: Licenses,
    },
    {
        path: baseUrl + '/tasks',
        name: 'tasks',
        component: ComingSoon,
    },
];
