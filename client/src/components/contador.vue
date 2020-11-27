<template>
    <v-container>
        <v-card>
            <v-card-text>
                <v-row>
                    <v-span class="title">{{datos.propiedad}}</v-span>    
                </v-row>
                <v-row>
                    <v-span class="subtitle-1">{{datos.prefijo}}{{datos.cantidad}}</v-span>
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