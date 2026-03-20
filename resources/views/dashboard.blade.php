<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-widest font-semibold">{{ now()->format('l, F d') }}</p>
                <h2 class="font-extrabold text-2xl text-gray-900 dark:text-white mt-0.5">Welcome back, {{ auth()->user()->name }}!</h2>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 rounded-full bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400 text-xs font-bold border border-blue-100 dark:border-blue-500/20">
                    📅 {{ now()->format('F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        {{-- ===== TOP ROW: Balance + Earnings + Spending Goal ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Balance Overview Widget --}}
            <div class="bg-white dark:bg-[#1a1a2e] rounded-2xl border border-gray-100 dark:border-gray-800 p-6 transition-shadow hover:shadow-lg md:col-span-2">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Balance Overview</h3>
                    <span class="text-xs text-gray-400">This Month</span>
                </div>
                <div class="flex items-baseline gap-3 mb-2">
                    <span class="text-4xl font-black tracking-tight {{ $balance >= 0 ? 'text-gray-900 dark:text-white' : 'text-red-500' }}">
                        {{ auth()->user()->currencySymbol() }}{{ number_format(abs($balance), 2) }}
                    </span>
                    @if($income > 0)
                    <span class="flex items-center gap-1 text-xs font-bold {{ $balance >= 0 ? 'text-emerald-500' : 'text-red-500' }} bg-{{ $balance >= 0 ? 'emerald' : 'red' }}-50 dark:bg-{{ $balance >= 0 ? 'emerald' : 'red' }}-500/10 px-2 py-0.5 rounded-full">
                        {{ $balance >= 0 ? '↑' : '↓' }} {{ number_format(abs(($balance / $income) * 100), 1) }}%
                    </span>
                    @endif
                </div>
                <div class="flex items-center gap-4 text-xs text-gray-400 mt-1">
                    <span>📊 {{ $recentTransactions->count() }} transactions</span>
                    <span>🏷️ {{ $recentTransactions->pluck('category_id')->unique()->count() }} categories</span>
                </div>

                {{-- Income vs Expense Bar --}}
                @if($income + $expenses > 0)
                <div class="mt-6 space-y-3">
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-emerald-500">Income</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">+{{ auth()->user()->currencySymbol() }}{{ number_format($income, 2) }}</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full transition-all duration-700" style="width: {{ $income > 0 ? min(100, ($income / max($income, $expenses)) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-semibold text-red-500">Expenses</span>
                                <span class="font-bold text-gray-700 dark:text-gray-300">-{{ auth()->user()->currencySymbol() }}{{ number_format($expenses, 2) }}</span>
                            </div>
                            <div class="w-full h-2.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-red-400 to-red-500 rounded-full transition-all duration-700" style="width: {{ $expenses > 0 ? min(100, ($expenses / max($income, $expenses)) * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- Earnings Widget --}}
            <div class="bg-white dark:bg-[#1a1a2e] rounded-2xl border border-gray-100 dark:border-gray-800 p-6 transition-shadow hover:shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Earnings</h3>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-black tracking-tight text-emerald-500">+{{ auth()->user()->currencySymbol() }}{{ number_format($income, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Total income this month</p>

                {{-- Monthly Savings --}}
                @if($income > 0)
                <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500">Month Goal</span>
                        <span class="text-xs font-bold {{ $balance >= 0 ? 'text-emerald-500' : 'text-red-500' }}">{{ number_format(max(0, ($balance / $income) * 100), 0) }}%</span>
                    </div>
                    {{-- Donut representation --}}
                    <div class="flex items-center justify-center">
                        <div class="relative w-28 h-28">
                            <svg class="w-28 h-28 transform -rotate-90" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="10" class="dark:stroke-gray-800"/>
                                <circle cx="60" cy="60" r="50" fill="none" stroke="{{ $balance >= 0 ? '#10B981' : '#EF4444' }}" stroke-width="10" stroke-linecap="round" stroke-dasharray="{{ min(100, max(0, ($balance / $income) * 100)) * 3.14 }} 314" class="transition-all duration-700"/>
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ number_format(max(0, ($balance / $income) * 100), 0) }}%</span>
                                <span class="text-[9px] text-gray-400 font-medium">Saved</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ===== BOTTOM ROW: Transactions + Spending ===== --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Transactions Widget --}}
            <div class="lg:col-span-2 bg-white dark:bg-[#1a1a2e] rounded-2xl border border-gray-100 dark:border-gray-800 transition-shadow hover:shadow-lg !p-0">
                <div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-800">
                    <h3 class="font-bold text-gray-900 dark:text-white">Transactions</h3>
                    <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-blue-500 hover:text-blue-600 transition-colors flex items-center gap-1">
                        View All
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="divide-y divide-gray-50 dark:divide-gray-800/50">
                    @forelse($recentTransactions as $txn)
                    <div class="px-6 py-3.5 flex items-center justify-between hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold"
                                style="background-color: {{ optional($txn->category)->color ?? '#6366f1' }}18; color: {{ optional($txn->category)->color ?? '#6366f1' }}">
                                {{ strtoupper(substr(optional($txn->category)->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $txn->description }}</p>
                                <p class="text-xs text-gray-400">{{ $txn->transaction_date->format('d M, g:i A') }}</p>
                            </div>
                        </div>
                        <span class="text-sm font-bold {{ optional($txn->category)->type === 'income' ? 'text-emerald-500' : 'text-gray-900 dark:text-white' }}">
                            {{ optional($txn->category)->type === 'income' ? '+' : '' }}{{ auth()->user()->currencySymbol() }}{{ number_format($txn->amount, 2) }}
                        </span>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <p class="text-sm text-gray-400">No transactions yet</p>
                        <a href="{{ route('transactions.index') }}" class="mt-2 inline-block text-xs font-bold text-blue-500">Add your first →</a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Spending Widget --}}
            <div class="bg-white dark:bg-[#1a1a2e] rounded-2xl border border-gray-100 dark:border-gray-800 p-6 transition-shadow hover:shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900 dark:text-white">Spending</h3>
                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-500/10 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">{{ auth()->user()->currencySymbol() }}{{ number_format($expenses, 2) }}</p>
                <p class="text-xs text-gray-400 mt-1">Total spent this month</p>

                {{-- Budget Breakdown --}}
                <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-800 space-y-4">
                    @forelse($budgets as $budget)
                        @php
                            $spent = \App\Models\Transaction::where('user_id', auth()->id())
                                ->where('category_id', $budget->category_id)
                                ->whereBetween('transaction_date', [now()->startOfMonth(), now()->endOfMonth()])
                                ->sum('amount');
                            $pct = $budget->amount_limit > 0 ? min(($spent / $budget->amount_limit) * 100, 100) : 0;
                            $barColor = $pct > 90 ? '#EF4444' : ($pct > 70 ? '#F59E0B' : '#10B981');
                        @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ optional($budget->category)->name }}</span>
                                <span class="text-[10px] font-bold" style="color: {{ $barColor }}">{{ number_format($pct, 0) }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full">
                                <div class="h-full rounded-full transition-all duration-700" style="width: {{ $pct }}%; background-color: {{ $barColor }}"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">No budgets set. <a href="{{ route('budgets.index') }}" class="text-blue-500 font-semibold">Set now →</a></p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== AI INSIGHTS ROW ===== --}}
        <div class="bg-white dark:bg-[#1a1a2e] rounded-2xl border border-gray-100 dark:border-gray-800 p-6 transition-shadow hover:shadow-lg !bg-gradient-to-br !from-gray-900 !to-gray-800 dark:!from-[#0f0f23] dark:!to-[#1a1a2e] !border-gray-800">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-white">AI Insights</h3>
                        <p class="text-xs text-gray-400">Smart analysis of your spending</p>
                    </div>
                </div>
                <button id="generate-insights-btn" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-colors flex items-center gap-2">
                    <span>Generate</span>
                    <div id="insights-loader" class="hidden w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </button>
            </div>
            <div id="insights-content" class="text-sm text-gray-300 leading-relaxed">
                <p class="text-gray-500 italic">Click "Generate" to get AI-powered tips based on your transactions.</p>
            </div>
        </div>

    </div>

    <script>
        document.getElementById('generate-insights-btn').addEventListener('click', async function() {
            const btn = this;
            const loader = document.getElementById('insights-loader');
            const content = document.getElementById('insights-content');

            btn.disabled = true;
            loader.classList.remove('hidden');
            content.style.opacity = '0.5';

            try {
                const response = await fetch('{{ route("dashboard.insights") }}');
                const result = await response.json();

                if (result.success) {
                    content.innerHTML = formatMarkdown(result.insights);
                    content.style.opacity = '1';
                } else {
                    content.innerHTML = `<p class="text-red-400">Error: ${result.message}</p>`;
                }
            } catch (error) {
                content.innerHTML = `<p class="text-red-400">Network error. Please try again.</p>`;
            } finally {
                btn.disabled = false;
                loader.classList.add('hidden');
                content.style.opacity = '1';
            }
        });

        function formatMarkdown(text) {
            return text.split('\n').map(line => {
                line = line.trim();
                if (line.startsWith('- ') || line.startsWith('* ')) return `<li class="ml-3 list-disc mb-1 text-gray-300">${line.substring(2)}</li>`;
                if (line.startsWith('**') && line.endsWith('**')) return `<p class="font-bold text-white mt-2 mb-1">${line.replace(/\*\*/g, '')}</p>`;
                return line ? `<p class="mb-1">${line}</p>` : '';
            }).join('');
        }
    </script>
</x-app-layout>
