<script setup>
defineProps({
    scores: Array
});
</script>

<template>
    <div class="p-6 max-w-3xl mx-auto text-white">
        <h1 class="text-3xl font-bold mb-4">🏆 Leaderboard</h1>
        <table class="w-full table-auto bg-stone-800 rounded-lg overflow-hidden">
            <thead class="bg-stone-700 text-left">
            <tr>
                <th class="px-4 py-2">Rank</th>
                <th class="px-4 py-2">Player</th>
                <th class="px-4 py-2">Score</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(entry, index) in scores" :key="entry.id" class="border-t border-stone-600">
                <td class="px-4 py-2 font-semibold">#{{ index + 1 }}</td>
                <td class="px-4 py-2">{{ entry.user.name }}</td>
                <td class="px-4 py-2">{{ entry.score }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>
