// Fungsi API Handler
const api = {
    async get(endpoint) {
        const res = await fetch(`/api/${endpoint}`);
        if(!res.ok) throw new Error(await res.text());
        return res.json();
    },

    async post(endpoint, data) {
        const res = await fetch(`/api/${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        if(!res.ok) throw new Error(await res.text());
        return res.json();
    },

    async delete(endpoint) {
        const res = await fetch(`/api/${endpoint}`, { method: 'DELETE' });
        if(!res.ok) throw new Error(await res.text());
        return res.json();
    }
};

// Manajemen Transaksi
const transactionManager = {
    transactions: [],
    currentEditId: null,
    financeChart: null,
    currentFilter: null,

    async init() {
        this.transactions = await api.get('transactions.php');
        this.initChart();
        this.updateAll();
    },

    initChart() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        this.financeChart = new Chart(ctx, {
            type: 'doughnut',
            data: this.chartData(),
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: { bodyFont: { size: 14 } }
                }
            }
        });
    },

    chartData() {
        const income = this.transactions
            .filter(t => t.type === 'income')
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);
        
        const expense = this.transactions
            .filter(t => t.type === 'expense')
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);

        return {
            labels: ['Pemasukan', 'Pengeluaran'],
            datasets: [{
                label: 'Statistik Keuangan',
                data: [income, expense],
                backgroundColor: ['#2ecc71', '#e74c3c'],
                borderWidth: 2,
                hoverOffset: 10
            }]
        };
    },

    async updateAll() {
        this.updateSummary();
        this.updateChart();
        this.renderTransactions();
    },

    updateSummary() {
        const income = this.transactions
            .filter(t => t.type === 'income')
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);
        
        const expense = this.transactions
            .filter(t => t.type === 'expense')
            .reduce((sum, t) => sum + parseFloat(t.amount), 0);
        
        document.getElementById('balance').textContent = 
            `Rp${(income - expense).toLocaleString('id-ID')}`;
        document.getElementById('income').textContent = 
            `Rp${income.toLocaleString('id-ID')}`;
        document.getElementById('expense').textContent = 
            `Rp${expense.toLocaleString('id-ID')}`;
    },

    updateChart() {
        this.financeChart.data = this.chartData();
        this.financeChart.update();
    },

    renderTransactions() {
        const container = document.getElementById('transactions');
        container.innerHTML = '';

        this.transactions.forEach(transaction => {
            const div = document.createElement('div');
            div.className = 'transaction-item';
            div.innerHTML = this.transactionHTML(transaction);
            container.appendChild(div);
        });
    },

    transactionHTML(transaction) {
        return `
            <div class="transaction-info">
                <div class="transaction-name">${transaction.name}</div>
                <small>${new Date(transaction.date).toLocaleDateString('id-ID')}</small>
                <span class="category-tag ${transaction.category}">
                    ${this.getCategoryIcon(transaction.category)} ${transaction.category}
                </span>
            </div>
            <div class="transaction-actions">
                <div class="amount ${transaction.type}">
                    ${transaction.type === 'income' ? '+' : '-'}Rp${transaction.amount}
                </div>
                <div class="action-buttons">
                    <button onclick="transactionManager.editTransaction(${transaction.id})">
                        ✏️ Edit
                    </button>
                    <button onclick="transactionManager.deleteTransaction(${transaction.id})">
                        🗑️ Hapus
                    </button>
                </div>
            </div>
        `;
    },

    getCategoryIcon(category) {
        const icons = {
            makanan: '🍔',
            transportasi: '🚗',
            belanja: '🛍️',
            hiburan: '🎮',
            gaji: '💼',
            lainnya: '❔'
        };
        return icons[category] || '📌';
    },

    async editTransaction(id) {
        const transaction = this.transactions.find(t => t.id === id);
        if (!transaction) return;

        this.currentEditId = id;
        document.getElementById('transactionName').value = transaction.name;
        document.getElementById('transactionAmount').value = transaction.amount;
        document.getElementById('transactionDate').value = transaction.date;
        document.getElementById('transactionCategory').value = transaction.category;
        document.getElementById('transactionType').value = transaction.type;
        document.getElementById('submitButton').textContent = '💾 Simpan Perubahan';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    async deleteTransaction(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) return;
        
        try {
            await api.delete(`transactions.php?id=${id}`);
            this.transactions = this.transactions.filter(t => t.id !== id);
            await this.updateAll();
        } catch (error) {
            alert('Gagal menghapus transaksi: ' + error.message);
        }
    }
};

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('financeChart')) {
        transactionManager.init();
    }

    document.getElementById('transactionForm')?.addEventListener('submit', async e => {
        e.preventDefault();
        
        const transaction = {
            name: document.getElementById('transactionName').value,
            amount: document.getElementById('transactionAmount').value,
            date: document.getElementById('transactionDate').value,
            category: document.getElementById('transactionCategory').value,
            type: document.getElementById('transactionType').value
        };

        try {
            if (transactionManager.currentEditId) {
                await api.post(`transactions.php?id=${transactionManager.currentEditId}`, transaction);
            } else {
                await api.post('transactions.php', transaction);
            }
            
            await transactionManager.init();
            document.getElementById('transactionForm').reset();
            transactionManager.currentEditId = null;
            document.getElementById('submitButton').textContent = '➕ Tambah Transaksi';
        } catch (error) {
            alert('Gagal menyimpan transaksi: ' + error.message);
        }
    });
});

// Toggle Theme
function toggleTheme() {
    const theme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = theme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    document.cookie = `theme=${newTheme}; path=/; max-age=31536000`;
    document.querySelector('.theme-toggle').textContent = 
        newTheme === 'dark' ? '🌙' : '🌞';
}