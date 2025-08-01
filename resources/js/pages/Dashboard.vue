<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user

import { ref, onMounted, onUnmounted, watch } from 'vue';
import axios from 'axios';

function saveScore() {
    axios.post('/score', { score: score.value })
        .then(() => console.log('Score saved'))
        .catch(err => console.error('Error saving score:', err));
}

const vaporHealth = ref(100);
const forgeTemp = ref(60);
const energy = ref(100);
const score = ref(0);
const swordProgress = ref(0);
const isDrinking = ref(false);
const gameOver = ref(false);

let mainInterval: ReturnType<typeof setInterval> | null = null;
let healthTimer = 0;

function addWood() {
    if (energy.value < 10 || isDrinking.value) return;

    energy.value -= 10; // reduce energy
    forgeTemp.value = Math.min(forgeTemp.value + 10, 100); // increase forge temp, max 100
    vaporHealth.value = Math.min(vaporHealth.value + 5, 100); // heal vapor slightly
}

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
        if (forgeTemp.value < 70) {
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

// Watch for game over or sword completion and save score
watch(gameOver, (value) => {
    if (value) saveScore();
});

watch(swordProgress, (value) => {
    if (value >= 100) saveScore();
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

function drinkBrew() {
    if (isDrinking.value) return;

    isDrinking.value = true;

    // Simulate brew drinking time (e.g. 3 seconds)
    setTimeout(() => {
        energy.value = Math.min(energy.value + 30, 100); // add 30 energy, max 100
        isDrinking.value = false;
    }, 3000); // 3 seconds
}
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <div class="mb-2">
                <a href="/leaderboard" class="text-blue-400 hover:underline font-semibold">
                    🏆 View Leaderboard
                </a>
            </div>
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
                        🔥 Keep the <span class="text-purple-300 font-bold">Vapor</span> alive by feeding the forge! If Vapor fades... the game ends. Vapor temp must be above 50° to forge the sword. Add wood to the forge to increase its temperature and heal Vapor. Drink brews to restore energy.
                    </p>
                    <p class="text-center text-xs text-purple-200 mb-1">Vapor Health</p>
                    <progress :value="vaporHealth" max="100" class="w-32 h-2 mb-2"></progress>
                    <img src="/images/vapor.jpg" alt="Vapor" class="h-32 w-32 transition-opacity duration-300" :style="{ opacity: vaporHealth / 100 }" />
                    <img src="/images/forge.jpg" alt="Vapor" class="h-32 w-32 transition-opacity duration-300" />
                    <progress :value="swordProgress" max="100" class="w-3/4 h-4"></progress>
                    <p class="text-lg mt-2">Sword Progress: {{ swordProgress }}%</p>
                </div>

                <!-- Controls -->
                <div v-if="isDrinking" class="flex justify-center gap-4 mt-6">
                    <p class="text-yellow-400 text-sm">🍺 Drinking brew...</p>
                </div>
                <div v-else class="flex justify-center gap-4 mt-6">
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

                <div v-if="swordProgress >= 100" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center text-3xl font-bold text-green-400">
                    Sword Forged! Final Score: {{ score }}
                    <a href="/leaderboard" class="mt-4 text-blue-400 underline text-lg font-semibold">🏆 View Leaderboard</a>
                </div>

            </div>
        </div>
    </AppLayout>
</template>
