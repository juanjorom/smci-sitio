<template>
    <v-containe>
        <l-map :zoom="zoom" :center="center" @update:zoom="zoomUpdated" @update:center="centerUpdated">
            <l-tile-layer :url="url" ></l-tile-layer>
            <div>
                <l-control ><v-text-field outlined label="Buscar" clearable placeholder="Unidad" dense prepend-icon="mdi-map-marker" background-color="white"></v-text-field> </l-control>
            </div>
            <div v-for="item in ubicacion" :key="item.deviceno">
            <l-marker :ref="item.deviceno" :lat-lng="item.coord" :icon="icon">
                <l-tooltip :options="{permanent:true}">{{item.carlicense}}</l-tooltip>
            </l-marker>
            </div>
        </l-map>
    </v-containe>
</template>

<script>
import {LMap, LTileLayer, LMarker, LTooltip, LControl} from 'vue2-leaflet'
import { mapGetters } from 'vuex';
import L from 'leaflet';

delete L.Icon.Default.prototype._getIconUrl;

L.Icon.Default.mergeOptions({
  iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
  iconUrl: require('leaflet/dist/images/marker-icon.png'),
  shadowUrl: require('leaflet/dist/images/marker-shadow.png')
});

export default {
    data () {
        return{
            url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            zoom: 12,
            center: [20.7163, -105.231857],
            icon: L.icon({
                iconUrl: './img/camion_conectado.png',
                iconSize: [20, 20],
                iconAnchor: [20, 20]
                })
        }
    },
    computed: {
        ...mapGetters({
            ubicacion: 'sock/getUbicacion'
        })
    },
    methods: {
        zoomUpdated (zoom) {
            this.zoom = zoom;
        },
        centerUpdated (center) {
            this.center = center;
        },
        boundsUpdated (bounds) {
            this.bounds = bounds;
        }
    },
    components: {
        LMap,
        LTileLayer,
        LMarker,
        LTooltip,
        LControl
    }
}
</script>

<style>

</style>