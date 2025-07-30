<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user

import { ref, onMounted, onUnmounted } from 'vue';

const vaporHealth = ref(100);
const forgeTemp = ref(60);
const energy = ref(100);
const score = ref(0);
const swordProgress = ref(0);
const isDrinking = ref(false);
const gameOver = ref(false);

let mainInterval: ReturnType<typeof setInterval> | null = null;
let healthTimer = 0;

const gameTick = () => {
    if (gameOver.value || swordProgress.value >= 100) return;

    // Score points every second while vapor is alive
    if (vaporHealth.value > 0) {
        score.value += 100;
    }

    // Forge cools down gradually
    forgeTemp.value = Math.max(0, forgeTemp.value - 1);

    // Drain energy slowly
    energy.value = Math.max(0, energy.value - 1);

    // Every 10 seconds, reduce vapor health IF forge is too cold
    healthTimer += 1;
    if (healthTimer >= 10) {
        if (forgeTemp.value < 30) {
            vaporHealth.value = Math.max(0, vaporHealth.value - 10);
        }
        healthTimer = 0;
    }

    // Progress sword forging if forge is hot and vapor is alive
    if (forgeTemp.value >= 50 && vaporHealth.value > 0) {
        swordProgress.value = Math.min(100, swordProgress.value + 1);
    }

    // Game over if vapor dies
    if (vaporHealth.value <= 0) {
        gameOver.value = true;
        clearInterval(mainInterval!);
    }

    // Win condition
    if (swordProgress.value >= 100) {
        clearInterval(mainInterval!);
    }
};

onMounted(() => {
    mainInterval = setInterval(gameTick, 1000); // Tick every second
});

onUnmounted(() => {
    if (mainInterval) clearInterval(mainInterval);
});

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
                        <p class="text-sm text-gray-400">Logged in as: <span class="font-bold">{{ user.name }}</span></p>
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
                    <p class="text-center text-white text-lg font-medium max-w-xl">
                        🔥 Keep the <span class="text-purple-300 font-bold">Vapor</span> alive by feeding the forge! If Vapor fades... the game ends.
                    </p>
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
