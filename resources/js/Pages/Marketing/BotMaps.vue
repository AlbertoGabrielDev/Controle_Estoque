<!-- resources/js/Pages/Business/Index.vue -->
<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import { Loader } from '@googlemaps/js-api-loader';

const props = defineProps({
    googleMapsApiKey: String,
});

const businesses = ref([]);
const loading = ref(false);
const error = ref(null);
const nextPageToken = ref(null);
const map = ref(null);
const markers = ref([]);
const infoWindow = ref(null);
const mapsUrl = ref('');
const searchMetadata = ref(null);

const loader = new Loader({
    apiKey: props.googleMapsApiKey,
    version: "weekly",
    libraries: ["places"]
});

const initMap = async (center = null) => {
    try {
        if (!props.googleMapsApiKey) {
            throw new Error('Chave do Google Maps não fornecida');
        }

        await loader.load();

        const { Map, InfoWindow } = await google.maps.importLibrary("maps");
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

        map.value = new Map(document.getElementById("map"), {
            center: center || { lat: -16.6800426, lng: -49.3385998 },
            zoom: 13,
            mapId: "DEMO_MAP_ID",
        });

        infoWindow.value = new InfoWindow();
    } catch (err) {
        console.error("Google Maps error:", err);

        if (err.message.includes('ApiTargetBlockedMapError')) {
            error.value = "Erro de configuração: A API do Google Maps está bloqueada para este projeto. Verifique as configurações no Google Cloud Console.";
        } else {
            error.value = "Erro ao carregar o Google Maps: " + err.message;
        }
    }
};

const extractBusinesses = async () => {
    if (!mapsUrl.value) {
        error.value = "Por favor, insira uma URL do Google Maps";
        return;
    }

    loading.value = true;
    error.value = null;
    businesses.value = [];

    try {
        const response = await axios.post(route('business.extract'), {
            maps_url: mapsUrl.value,
        });

        businesses.value = response.data.businesses;
        nextPageToken.value = response.data.next_page_token;
        searchMetadata.value = response.data.search_metadata;

        // Se o mapa ainda não foi inicializado
        if (!map.value) {
            await initMap({
                lat: searchMetadata.value.latitude,
                lng: searchMetadata.value.longitude
            });
        } else {
            map.value.setCenter({
                lat: searchMetadata.value.latitude,
                lng: searchMetadata.value.longitude
            });
        }

        updateMarkers();
    } catch (err) {
        error.value = "Erro ao extrair dados: " + (err.response?.data?.error || err.message);
        console.error(err);
    } finally {
        loading.value = false;
    }
};

const updateMarkers = async () => {
    if (!map.value) return;

    const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

    // Limpa marcadores antigos
    markers.value.forEach(marker => marker.map = null);
    markers.value = [];

    // Adiciona novos marcadores
    businesses.value.forEach(business => {
        const marker = new AdvancedMarkerElement({
            map: map.value,
            position: { lat: business.latitude, lng: business.longitude },
            title: business.name,
        });

        marker.addListener("click", () => {
            infoWindow.value.setContent(`
                <div class="p-2">
                    <h3 class="font-bold">${business.name}</h3>
                    <p>${business.address}</p>
                    ${business.rating ? `<p>Avaliação: ${business.rating} (${business.total_ratings} avaliações)</p>` : ''}
                    ${business.phone ? `<p>Telefone: ${business.phone}</p>` : 'Sem telefone disponível'}
                    ${business.website ? `<p><a href="${business.website}" target="_blank">Website</a></p>` : ''}
                    ${business.open_now !== null ? `<p>${business.open_now ? 'Aberto agora' : 'Fechado agora'}</p>` : ''}
                    ${business.business_status ? `<p>Status: ${business.business_status}</p>` : ''}
                    ${business.types ? `<p class="text-xs text-gray-500 mt-1">${business.types.join(', ')}</p>` : ''}
                </div>
            `);
            infoWindow.value.open({
                anchor: marker,
                map: map.value,
            });
        });

        markers.value.push(marker);
    });
};


const loadMore = async () => {
    if (!nextPageToken.value) return;

    loading.value = true;

    try {
        const response = await axios.post(route('business.extract'), {
            maps_url: mapsUrl.value,
            next_page_token: nextPageToken.value,
        });

        // Adiciona os novos negócios aos existentes
        businesses.value = [...businesses.value, ...response.data.businesses];
        nextPageToken.value = response.data.next_page_token;
        updateMarkers();

        // Mostra mensagem se não há mais resultados
        if (!nextPageToken.value) {
            error.value = "Todos os resultados disponíveis foram carregados.";
        }
    } catch (err) {
        error.value = "Erro ao carregar mais resultados: " + err.message;
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    // Inicializa o mapa vazio
    initMap();
});
</script>

<template>

    <Head title="Extrator de Empresas do Google Maps" />

    <div class="min-h-screen bg-gray-100">
        <div class="py-6 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <h1 class="text-2xl font-bold text-gray-900 mb-6">Extrator de Empresas do Google Maps</h1>

                <div class="bg-white p-6 rounded-lg shadow mb-6">
                    <div class="space-y-4">
                        <div>
                            <label for="maps-url" class="block text-sm font-medium text-gray-700 mb-1">URL do Google
                                Maps</label>
                            <input type="url" id="maps-url" v-model="mapsUrl"
                                placeholder="Cole aqui a URL completa do Google Maps"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" />
                            <p class="mt-1 text-sm text-gray-500">
                                Exemplo:
                                https://www.google.com/maps/search/restaurante+em+são+paulo/@-23.5505199,-46.6333094,14z
                            </p>
                        </div>

                        <button @click="extractBusinesses" :disabled="loading"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md disabled:opacity-50">
                            <span v-if="loading">Processando...</span>
                            <span v-else>Extrair Empresas</span>
                        </button>

                        <div v-if="error" class="p-3 bg-red-50 text-red-700 rounded-md">
                            {{ error }}
                        </div>
                    </div>
                </div>

                <div v-if="searchMetadata" class="mb-4 bg-white p-4 rounded-lg shadow">
                    <h2 class="text-lg font-medium mb-2">Informações da Busca</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Localização</p>
                            <p class="font-medium">{{ searchMetadata.latitude }}, {{ searchMetadata.longitude }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Raio aproximado</p>
                            <p class="font-medium">{{ searchMetadata.radius }} metros</p>
                        </div>
                        <div v-if="searchMetadata.query">
                            <p class="text-sm text-gray-500">Termo buscado</p>
                            <p class="font-medium">{{ searchMetadata.query }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div id="map" class="h-96 w-full rounded-lg shadow"></div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-white p-4 rounded-lg shadow">
                            <h2 class="text-lg font-medium mb-4">Empresas encontradas ({{ businesses.length }})</h2>

                            <div v-if="loading && businesses.length === 0" class="text-center py-4">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                                <p class="mt-2">Extraindo dados...</p>
                            </div>

                            <div v-else-if="businesses.length === 0" class="text-center py-4 text-gray-500">
                                Nenhuma empresa encontrada. Cole uma URL do Google Maps e clique em "Extrair Empresas".
                            </div>

                            <ul v-else class="divide-y divide-gray-200 max-h-[600px] overflow-y-auto">
                                <li v-for="business in businesses" :key="business.id" class="py-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-sm font-medium text-gray-900 truncate">{{ business.name }}
                                            </h3>
                                            <p class="text-sm text-gray-500 truncate">{{ business.address }}</p>
                                            <p v-if="business.phone" class="text-sm text-gray-600 mt-1">
                                                📞 {{ business.phone }}
                                            </p>
                                            <div class="flex items-center mt-1">
                                                <span v-if="business.rating" class="text-yellow-500">★ {{
                                                    business.rating }}</span>
                                                <span v-if="business.total_ratings"
                                                    class="text-xs text-gray-500 ml-1">({{ business.total_ratings
                                                    }})</span>
                                                <span v-if="business.open_now !== null"
                                                    :class="business.open_now ? 'text-green-500' : 'text-red-500'"
                                                    class="text-xs ml-2">
                                                    {{ business.open_now ? 'Aberto' : 'Fechado' }}
                                                </span>
                                            </div>
                                            <div v-if="business.types" class="mt-1">
                                                <span v-for="type in business.types.slice(0, 3)" :key="type"
                                                    class="inline-block bg-gray-100 rounded-full px-2 py-1 text-xs text-gray-600 mr-1 mb-1">
                                                    {{ type }}
                                                </span>
                                            </div>
                                        </div>
                                        <button @click="map.panTo({ lat: business.latitude, lng: business.longitude })"
                                            class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                                            Ver
                                        </button>
                                    </div>
                                </li>
                            </ul>

                            <button v-if="nextPageToken && !loading" @click="loadMore"
                                class="mt-4 w-full bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                                Carregar mais resultados
                            </button>

                            <div v-if="loading && businesses.length > 0" class="text-center py-2">
                                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500 mx-auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
#map {
    height: 600px;
}

.gm-style .gm-style-iw-c {
    padding: 0 !important;
    max-width: 300px !important;
}

.gm-style .gm-style-iw-d {
    padding: 0 !important;
    overflow: hidden !important;
}
</style>