<div class="p-6 bg-white rounded-lg shadow-sm border space-y-6">
    <h2 class="text-2xl font-bold text-gray-800">💼 Agent Earnings & Wallet</h2>

    @if (session()->has('message'))
        <div class="p-4 bg-green-100 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <!-- Balance Summary Widgets -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-4 bg-blue-50 border border-blue-100 rounded-lg">
            <span class="text-xs font-semibold text-blue-600 uppercase">Total Referrals</span>
            <h3 class="text-2xl font-bold text-blue-900 mt-1">{{ $total_referrals }} Patients</h3>
        </div>
        <div class="p-4 bg-green-50 border border-green-100 rounded-lg">
            <span class="text-xs font-semibold text-green-600 uppercase">Total Commission</span>
            <h3 class="text-2xl font-bold text-green-900 mt-1">৳{{ number_format($total_commission, 2) }}</h3>
        </div>
        <div class="p-4 bg-purple-50 border border-purple-100 rounded-lg">
            <span class="text-xs font-semibold text-purple-600 uppercase">Available Balance</span>
            <h3 class="text-2xl font-bold text-purple-900 mt-1">৳{{ number_format($available_balance, 2) }}</h3>
        </div>
        <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
            <span class="text-xs font-semibold text-yellow-600 uppercase">Pending Balance</span>
            <h3 class="text-2xl font-bold text-yellow-900 mt-1">৳{{ number_format($pending_balance, 2) }}</h3>
        </div>
    </div>

    <!-- Live Payout Request Form -->
    <div class="p-4 bg-gray-50 rounded-lg space-y-4">
        <h4 class="font-bold text-gray-800">💸 Request Commission Withdrawal</h4>
        
        <form wire:submit.prevent="submitWithdraw" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Amount (৳) *</label>
                    <input type="number" step="0.01" wire:model="withdraw_amount" placeholder="Min ৳500" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    @error('withdraw_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                    <select wire:model="payment_method" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                        <option value="bKash">bKash Personal</option>
                        <option value="Nagad">Nagad Personal</option>
                        <option value="Bank">Bank Account Transfer</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Account / Phone Number *</label>
                    <input type="text" wire:model="account_number" placeholder="01700000000" class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                    @error('account_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md shadow">
                Submit Payout Request
            </button>
        </form>
    </div>
</div>
