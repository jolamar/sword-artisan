<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Game Wrapper -->
            <div class="relative min-h-[100vh] flex flex-col rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border bg-stone-800 text-white">

                <!-- Top HUD -->
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="font-semibold">Vapor Health: {{ vaporHealth }}</p>
                        <p class="font-semibold">Forge Temp: {{ forgeTemp }}°</p>
                    </div>
                    <div>
                        <p class="font-semibold">Energy: {{ energy }}</p>
                        <p class="font-semibold">Score: {{ score }}</p>
                    </div>
                </div>

                <!-- Game Canvas -->
                <div class="flex-1 flex flex-col items-center justify-center gap-4">
                    <img src="/images/vapor.jpg" alt="Vapor" class="h-32 w-32 transition-opacity duration-300" :style="{ opacity: vaporHealth / 100 }" />
                    <img src="/images/forge.jpg" alt="Vapor" class="h-32 w-32 transition-opacity duration-300" />
                    <progress :value="swordProgress" max="100" class="w-3/4 h-4"></progress>
                    <p class="text-lg mt-2">Sword Progress: {{ swordProgress }}%</p>
                </div>

                <!-- Controls -->
                <div class="flex justify-center gap-4 mt-6">
                    <button @click="addWood" :disabled="energy < 10 || isDrinking"
                            class="px-4 py-2 bg-amber-600 hover:bg-amber-700 rounded-lg disabled:opacity-50">
                        Add Wood
                    </button>
                    <button @click="drinkBrew" :disabled="isDrinking"
                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 rounded-lg disabled:opacity-50">
                        Drink Brew
                    </button>
                </div>

                <div v-if="gameOver" class="absolute inset-0 bg-black/80 flex items-center justify-center text-3xl font-bold text-white">
                    Game Over - Score: {{ score }}
                </div>

                <div v-if="swordProgress >= 100" class="absolute inset-0 bg-black/80 flex items-center justify-center text-3xl font-bold text-green-400">
                    Sword Forged! Final Score: {{ score }}
                </div>

            </div>
        </div>
    </AppLayout>
</template>
