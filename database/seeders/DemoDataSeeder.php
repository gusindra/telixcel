<?php

namespace Database\Seeders;

use App\Models\Billing;
use App\Models\Client;
use App\Models\CommerceItem;
use App\Models\Contract;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\ProductLine;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = 1;
        $teamId = 1;
        $companyId = 1;
        $now = Carbon::now();

        // ──────────────────────────────────────
        // CLIENTS
        // ──────────────────────────────────────
        $clients = [];
        $clientNames = [
            ['name' => 'PT Maju Teknologi', 'phone' => '081234567890', 'email' => 'info@majuteknologi.com', 'address' => 'Jl. Sudirman No. 45, Jakarta Pusat'],
            ['name' => 'PT Digital Nusantara', 'phone' => '085678901234', 'email' => 'contact@digitalnusantara.id', 'address' => 'Jl. Gatot Subroto No. 12, Jakarta Selatan'],
            ['name' => 'CV Solusi Inovasi', 'phone' => '089876543210', 'email' => 'admin@solusiinovasi.co.id', 'address' => 'Jl. Diponegoro No. 88, Bandung'],
            ['name' => 'PT Infrastruktur Prima', 'phone' => '082112233445', 'email' => 'hello@infrastrukturprima.com', 'address' => 'Jl. HR Rasuna Said No. 67, Jakarta Selatan'],
            ['name' => 'PT Data Cerdas Indonesia', 'phone' => '087778889990', 'email' => 'support@datacerdas.id', 'address' => 'Jl. Kuningan No. 23, Jakarta Pusat'],
        ];

        foreach ($clientNames as $c) {
            $clients[] = Client::updateOrCreate(['email' => $c['email']], [
                'uuid' => (string) Str::uuid(),
                'name' => $c['name'],
                'phone' => $c['phone'],
                'address' => $c['address'],
                'user_id' => $adminId,
                'tag' => 'demo',
            ]);
        }

        // ──────────────────────────────────────
        // PRODUCT LINES & COMMERCE ITEMS
        // ──────────────────────────────────────
        $plCloud = ProductLine::updateOrCreate(['name' => 'Cloud Services'], ['type' => 'service', 'user_id' => $adminId]);
        $plDev = ProductLine::updateOrCreate(['name' => 'Development'], ['type' => 'service', 'user_id' => $adminId]);
        $plHard = ProductLine::updateOrCreate(['name' => 'Hardware'], ['type' => 'product', 'user_id' => $adminId]);

        $products = [];
        $productData = [
            ['sku' => 'CLD-HOST', 'name' => 'Cloud Hosting - Professional', 'type' => 'monthly', 'unit_price' => 2500000, 'product_line' => $plCloud->id],
            ['sku' => 'CLD-BKP', 'name' => 'Cloud Backup 500GB', 'type' => 'monthly', 'unit_price' => 750000, 'product_line' => $plCloud->id],
            ['sku' => 'DEV-WEB', 'name' => 'Web App Development', 'type' => 'one_time', 'unit_price' => 50000000, 'product_line' => $plDev->id],
            ['sku' => 'DEV-MOB', 'name' => 'Mobile App Development', 'type' => 'one_time', 'unit_price' => 75000000, 'product_line' => $plDev->id],
            ['sku' => 'DEV-API', 'name' => 'API Integration', 'type' => 'one_time', 'unit_price' => 15000000, 'product_line' => $plDev->id],
            ['sku' => 'HRD-SRV', 'name' => 'Server Rack - Dell', 'type' => 'one_time', 'unit_price' => 35000000, 'product_line' => $plHard->id],
            ['sku' => 'HRD-NET', 'name' => 'Network Switch - Cisco', 'type' => 'one_time', 'unit_price' => 8500000, 'product_line' => $plHard->id],
        ];

        foreach ($productData as $pd) {
            $products[] = CommerceItem::updateOrCreate(['sku' => $pd['sku']], [
                'name' => $pd['name'],
                'type' => $pd['type'],
                'unit_price' => $pd['unit_price'],
                'unit' => $pd['type'] === 'monthly' ? 'month' : 'pcs',
                'product_line' => $pd['product_line'],
                'user_id' => $adminId,
                'status' => 'active',
            ]);
        }

        // ──────────────────────────────────────
        // PROJECTS
        // ──────────────────────────────────────
        $projects = [];
        $projectData = [
            [
                'name' => 'Sistem ERP Cloud PT Maju Teknologi',
                'type' => 'Development',
                'status' => 'active',
                'customer_name' => $clients[0]->name,
                'customer_address' => $clients[0]->address,
                'referrer_name' => 'Andi Pratama',
                'product_line' => $plDev->id,
                'clients' => [$clients[0]],
            ],
            [
                'name' => 'Migrasi Data Center PT Digital Nusantara',
                'type' => 'Infrastructure',
                'status' => 'active',
                'customer_name' => $clients[1]->name,
                'customer_address' => $clients[1]->address,
                'referrer_name' => 'Budi Santoso',
                'product_line' => $plCloud->id,
                'clients' => [$clients[1]],
            ],
            [
                'name' => 'Pengadaan Server & Jaringan CV Solusi Inovasi',
                'type' => 'Procurement',
                'status' => 'draft',
                'customer_name' => $clients[2]->name,
                'customer_address' => $clients[2]->address,
                'referrer_name' => 'Citra Dewi',
                'product_line' => $plHard->id,
                'clients' => [$clients[2], $clients[3]],
            ],
        ];

        foreach ($projectData as $i => $pd) {
            $project = Project::updateOrCreate(['name' => $pd['name']], [
                'type' => $pd['type'],
                'status' => $pd['status'],
                'entity_party' => $companyId,
                'customer_name' => $pd['customer_name'],
                'customer_address' => $pd['customer_address'],
                'referrer_name' => $pd['referrer_name'],
                'team_id' => $teamId,
                'product_line' => $pd['product_line'],
            ]);

            foreach ($pd['clients'] as $client) {
                $project->clients()->syncWithoutDetaching([$client->id]);
            }

            $projects[] = $project;
        }

        // ──────────────────────────────────────
        // TASKS
        // ──────────────────────────────────────
        $tasks = [];

        // --- Project 1 tasks ---
        $t1 = Task::create([
            'project_id' => $projects[0]->id,
            'title' => 'Setup database & environment staging',
            'type' => 'operasional',
            'priority' => 'high',
            'owner_id' => $adminId,
            'target_date' => $now->copy()->addDays(3),
            'status' => 'complete',
            'team_id' => $teamId,
        ]);
        $tasks[] = $t1;

        $t2 = Task::create([
            'project_id' => $projects[0]->id,
            'title' => 'Develop module HR & Payroll',
            'type' => 'operasional',
            'priority' => 'high',
            'owner_id' => 2,
            'target_date' => $now->copy()->addDays(14),
            'status' => 'progress',
            'team_id' => $teamId,
        ]);
        $tasks[] = $t2;

        // Subtasks under t2
        Task::create([
            'project_id' => $projects[0]->id,
            'parent_id' => $t2->id,
            'title' => 'UI/UX halaman payroll',
            'type' => 'operasional',
            'priority' => 'high',
            'owner_id' => 2,
            'target_date' => $now->copy()->addDays(7),
            'status' => 'progress',
            'team_id' => $teamId,
        ]);
        Task::create([
            'project_id' => $projects[0]->id,
            'parent_id' => $t2->id,
            'title' => 'API integrasi bank untuk payroll',
            'type' => 'finance',
            'priority' => 'high',
            'owner_id' => 3,
            'target_date' => $now->copy()->addDays(10),
            'status' => 'pending',
            'team_id' => $teamId,
        ]);
        Task::create([
            'project_id' => $projects[0]->id,
            'parent_id' => $t2->id,
            'title' => 'Testing unit payroll module',
            'type' => 'operasional',
            'priority' => 'medium',
            'owner_id' => 4,
            'target_date' => $now->copy()->addDays(12),
            'status' => 'pending',
            'team_id' => $teamId,
        ]);

        $t3 = Task::create([
            'project_id' => $projects[0]->id,
            'title' => 'Develop module Inventory',
            'type' => 'operasional',
            'priority' => 'medium',
            'owner_id' => 3,
            'target_date' => $now->copy()->addDays(21),
            'status' => 'progress',
            'team_id' => $teamId,
        ]);
        $tasks[] = $t3;

        $t4 = Task::create([
            'project_id' => $projects[0]->id,
            'title' => 'UAT & user training',
            'type' => 'admin',
            'priority' => 'medium',
            'owner_id' => $adminId,
            'target_date' => $now->copy()->addDays(35),
            'status' => 'pending',
            'team_id' => $teamId,
        ]);
        $tasks[] = $t4;

        // Overdue task
        Task::create([
            'project_id' => $projects[0]->id,
            'title' => 'Buat laporan initial requirement',
            'type' => 'admin',
            'priority' => 'high',
            'owner_id' => 2,
            'target_date' => $now->copy()->subDays(5),
            'status' => 'progress',
            'team_id' => $teamId,
        ]);

        // --- Project 2 tasks ---
        Task::create([
            'project_id' => $projects[1]->id,
            'title' => 'Audit existing infrastructure',
            'type' => 'operasional',
            'priority' => 'high',
            'owner_id' => $adminId,
            'target_date' => $now->copy()->addDays(5),
            'status' => 'complete',
            'team_id' => $teamId,
        ]);

        $t5 = Task::create([
            'project_id' => $projects[1]->id,
            'title' => 'Migrasi server production',
            'type' => 'operasional',
            'priority' => 'high',
            'owner_id' => 2,
            'target_date' => $now->copy()->addDays(7),
            'status' => 'progress',
            'team_id' => $teamId,
        ]);
        $tasks[] = $t5;

        Task::create([
            'project_id' => $projects[1]->id,
            'title' => 'Setup monitoring & alert system',
            'type' => 'operasional',
            'priority' => 'medium',
            'owner_id' => 3,
            'target_date' => $now->copy()->addDays(10),
            'status' => 'pending',
            'team_id' => $teamId,
        ]);

        // --- Project 3 tasks ---
        Task::create([
            'project_id' => $projects[2]->id,
            'title' => 'Survey kebutuhan hardware',
            'type' => 'admin',
            'priority' => 'medium',
            'owner_id' => 4,
            'target_date' => $now->copy()->addDays(7),
            'status' => 'pending',
            'team_id' => $teamId,
        ]);

        Task::create([
            'project_id' => $projects[2]->id,
            'title' => 'Buat RAB procurement',
            'type' => 'finance',
            'priority' => 'high',
            'owner_id' => $adminId,
            'target_date' => $now->copy()->addDays(10),
            'status' => 'pending',
            'team_id' => $teamId,
        ]);

        // ──────────────────────────────────────
        // CONTRACTS
        // ──────────────────────────────────────
        Contract::updateOrCreate(['title' => 'Kontrak ERP - PT Maju Teknologi'], [
            'status' => 'active',
            'signer_email' => $clients[0]->email,
            'user_id' => $adminId,
            'actived_at' => $now->copy()->subMonths(2),
            'expired_at' => $now->copy()->addMonths(10),
            'model' => 'PROJECT',
            'model_id' => $projects[0]->id,
            'client_id' => $clients[0]->id,
        ]);

        Contract::updateOrCreate(['title' => 'Kontrak Migrasi Data - PT Digital Nusantara'], [
            'status' => 'active',
            'signer_email' => $clients[1]->email,
            'user_id' => $adminId,
            'actived_at' => $now->copy()->subMonth(),
            'expired_at' => $now->copy()->addMonths(3),
            'model' => 'PROJECT',
            'model_id' => $projects[1]->id,
            'client_id' => $clients[1]->id,
        ]);

        // Expiring soon
        Contract::updateOrCreate(['title' => 'Kontrak Maintenance Server - PT Infrastruktur Prima'], [
            'status' => 'active',
            'signer_email' => $clients[3]->email,
            'user_id' => $adminId,
            'actived_at' => $now->copy()->subYear(),
            'expired_at' => $now->copy()->addDays(20),
            'model' => 'CLIENT',
            'model_id' => $clients[3]->id,
            'client_id' => $clients[3]->id,
        ]);

        // ──────────────────────────────────────
        // ORDERS
        // ──────────────────────────────────────
        $order1 = Order::create([
            'no' => 'ORD-2026-001',
            'name' => 'Pembelian Web App Development - PT Maju Teknologi',
            'type' => 'Selling Product',
            'entity_party' => $companyId,
            'customer_type' => 'company',
            'vat' => 11,
            'total' => 50000000 * 1.11,
            'customer_id' => $clients[0]->uuid,
            'user_id' => $adminId,
            'source' => 'PROJECT',
            'source_id' => $projects[0]->id,
            'status' => 'active',
            'date' => $now->copy()->subMonths(1),
        ]);

        OrderProduct::create([
            'name' => $products[2]->name,
            'model' => 'Order',
            'model_id' => $order1->id,
            'product_id' => $products[2]->id,
            'qty' => 1,
            'unit' => 'pcs',
            'price' => $products[2]->unit_price,
            'user_id' => $adminId,
        ]);

        Billing::create([
            'order_id' => $order1->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'INV-2026-001',
            'description' => 'Invoice Web App Development',
            'amount' => $order1->total,
            'status' => 'paid',
            'direction' => 'out',
            'period' => $now->copy()->subMonths(1)->format('Y-m'),
            'invoice_date' => $now->copy()->subMonths(1),
            'user_id' => $adminId,
        ]);

        $order2 = Order::create([
            'no' => 'ORD-2026-002',
            'name' => 'Cloud Hosting 12 bulan - PT Maju Teknologi',
            'type' => 'SAAS Service',
            'entity_party' => $companyId,
            'customer_type' => 'company',
            'vat' => 11,
            'total' => 2500000 * 12 * 1.11,
            'customer_id' => $clients[0]->uuid,
            'user_id' => $adminId,
            'source' => 'PROJECT',
            'source_id' => $projects[0]->id,
            'status' => 'active',
            'date' => $now->copy()->subMonths(2),
        ]);

        OrderProduct::create([
            'name' => $products[0]->name,
            'model' => 'Order',
            'model_id' => $order2->id,
            'product_id' => $products[0]->id,
            'qty' => 12,
            'unit' => 'month',
            'price' => $products[0]->unit_price,
            'user_id' => $adminId,
        ]);

        Billing::create([
            'order_id' => $order2->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'INV-2026-002',
            'description' => 'Invoice Cloud Hosting 12 bulan',
            'amount' => $order2->total,
            'status' => 'unpaid',
            'direction' => 'out',
            'period' => $now->format('Y-m'),
            'invoice_date' => $now,
            'user_id' => $adminId,
        ]);

        $order3 = Order::create([
            'no' => 'ORD-2026-003',
            'name' => 'Migrasi Data Center - PT Digital Nusantara',
            'type' => 'Selling Product',
            'entity_party' => $companyId,
            'customer_type' => 'company',
            'vat' => 11,
            'total' => 150000000 * 1.11,
            'customer_id' => $clients[1]->uuid,
            'user_id' => $adminId,
            'source' => 'PROJECT',
            'source_id' => $projects[1]->id,
            'status' => 'draft',
            'date' => $now,
        ]);

        OrderProduct::create([
            'name' => $products[0]->name,
            'model' => 'Order',
            'model_id' => $order3->id,
            'product_id' => $products[0]->id,
            'qty' => 6,
            'unit' => 'month',
            'price' => 2500000,
            'user_id' => $adminId,
        ]);

        OrderProduct::create([
            'name' => $products[4]->name,
            'model' => 'Order',
            'model_id' => $order3->id,
            'product_id' => $products[4]->id,
            'qty' => 1,
            'unit' => 'pcs',
            'price' => 15000000,
            'user_id' => $adminId,
        ]);

        OrderProduct::create([
            'name' => $products[1]->name,
            'model' => 'Order',
            'model_id' => $order3->id,
            'product_id' => $products[1]->id,
            'qty' => 6,
            'unit' => 'month',
            'price' => 750000,
            'user_id' => $adminId,
        ]);

        Billing::create([
            'order_id' => $order3->id,
            'uuid' => (string) Str::uuid(),
            'code' => 'INV-2026-003',
            'description' => 'DP Migrasi Data Center 50%',
            'amount' => $order3->total * 0.5,
            'status' => 'unpaid',
            'direction' => 'out',
            'period' => $now->format('Y-m'),
            'invoice_date' => $now,
            'user_id' => $adminId,
        ]);

        $order4 = Order::create([
            'no' => 'ORD-2026-004',
            'name' => 'Server & Jaringan - CV Solusi Inovasi',
            'type' => 'Selling Product',
            'entity_party' => $companyId,
            'customer_type' => 'company',
            'vat' => 11,
            'total' => (35000000 + 8500000 * 2) * 1.11,
            'customer_id' => $clients[2]->uuid,
            'user_id' => $adminId,
            'source' => 'PROJECT',
            'source_id' => $projects[2]->id,
            'status' => 'draft',
            'date' => $now,
        ]);

        OrderProduct::create([
            'name' => $products[5]->name,
            'model' => 'Order',
            'model_id' => $order4->id,
            'product_id' => $products[5]->id,
            'qty' => 1,
            'unit' => 'pcs',
            'price' => $products[5]->unit_price,
            'user_id' => $adminId,
        ]);

        OrderProduct::create([
            'name' => $products[6]->name,
            'model' => 'Order',
            'model_id' => $order4->id,
            'product_id' => $products[6]->id,
            'qty' => 2,
            'unit' => 'pcs',
            'price' => $products[6]->unit_price,
            'user_id' => $adminId,
        ]);

        echo "Demo data seeded: " . count($clients) . " clients, "
            . count($projects) . " projects, "
            . count($productData) . " products, 4 orders, 3 contracts.\n";
    }
}
