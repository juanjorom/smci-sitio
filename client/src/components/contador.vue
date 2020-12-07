<template>
    <v-container>
        <v-card>
            <v-card-text>
                <v-row>
                    <span class="title">{{datos.propiedad}}</span>    
                </v-row>
                <v-row>
                    <span class="subtitle-1">{{datos.prefijo}}{{datos.cantidad}}</span>
                </v-row>
            </v-card-text>
        </v-card>
    </v-container>
</template>

<script>
export default {
    props: {
        datos: Object
    }
}
</script>

<style>

</style>