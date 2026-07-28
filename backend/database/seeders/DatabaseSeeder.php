<?php

namespace Database\Seeders;

use App\Infrastructure\Persistence\Models\BranchModel;
use App\Infrastructure\Persistence\Models\CourierModel;
use App\Infrastructure\Persistence\Models\PackageModel;
use App\Infrastructure\Persistence\Models\StatusHistoryModel;
use App\Infrastructure\Persistence\Models\VehicleModel;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Aeroflash',
            'email' => 'admin@aeroflash.com',
            'password' => 'password',
        ]);

        $cdmx = BranchModel::create([
            'name' => 'CDMX Centro',
            'address' => 'Av. Reforma 222, Cuauhtémoc',
            'phone' => '55-1111-0001',
        ]);

        $gdl = BranchModel::create([
            'name' => 'Guadalajara Norte',
            'address' => 'Av. Vallarta 1500, Zapopan',
            'phone' => '33-2222-0002',
        ]);

        $mty = BranchModel::create([
            'name' => 'Monterrey Sur',
            'address' => 'Av. Garza Sada 2500, Monterrey',
            'phone' => '81-3333-0003',
        ]);

        $courier1 = CourierModel::create([
            'name' => 'Carlos López',
            'email' => 'carlos.lopez@aeroflash.com',
            'phone' => '55-1000-0001',
            'branch_id' => $cdmx->id,
            'is_active' => true,
        ]);

        $courier2 = CourierModel::create([
            'name' => 'María García',
            'email' => 'maria.garcia@aeroflash.com',
            'phone' => '55-1000-0002',
            'branch_id' => $cdmx->id,
            'is_active' => true,
        ]);

        $courier3 = CourierModel::create([
            'name' => 'Juan Hernández',
            'email' => 'juan.hernandez@aeroflash.com',
            'phone' => '33-2000-0003',
            'branch_id' => $gdl->id,
            'is_active' => true,
        ]);

        $courier4 = CourierModel::create([
            'name' => 'Ana Martínez',
            'email' => 'ana.martinez@aeroflash.com',
            'phone' => '33-2000-0004',
            'branch_id' => $gdl->id,
            'is_active' => true,
        ]);

        $courier5 = CourierModel::create([
            'name' => 'Pedro Ramírez',
            'email' => 'pedro.ramirez@aeroflash.com',
            'phone' => '81-3000-0005',
            'branch_id' => $mty->id,
            'is_active' => true,
        ]);

        $courier6 = CourierModel::create([
            'name' => 'Laura Díaz',
            'email' => 'laura.diaz@aeroflash.com',
            'phone' => '81-3000-0006',
            'branch_id' => $mty->id,
            'is_active' => true,
        ]);

        $vehicle1 = VehicleModel::create([
            'plate_number' => 'ABC-1234',
            'type' => 'Van',
            'brand' => 'Ford',
            'model' => 'Transit 2024',
            'courier_id' => $courier1->id,
            'branch_id' => $cdmx->id,
            'is_active' => true,
        ]);

        $vehicle2 = VehicleModel::create([
            'plate_number' => 'DEF-5678',
            'type' => 'Motorcycle',
            'brand' => 'Honda',
            'model' => 'Cargo 2024',
            'courier_id' => $courier2->id,
            'branch_id' => $cdmx->id,
            'is_active' => true,
        ]);

        $vehicle3 = VehicleModel::create([
            'plate_number' => 'GHI-9012',
            'type' => 'Van',
            'brand' => 'Chevrolet',
            'model' => 'Express 2024',
            'courier_id' => $courier3->id,
            'branch_id' => $gdl->id,
            'is_active' => true,
        ]);

        $vehicle4 = VehicleModel::create([
            'plate_number' => 'JKL-3456',
            'type' => 'Motorcycle',
            'brand' => 'Yamaha',
            'model' => 'NMax 2024',
            'courier_id' => $courier4->id,
            'branch_id' => $gdl->id,
            'is_active' => true,
        ]);

        $vehicle5 = VehicleModel::create([
            'plate_number' => 'MNO-7890',
            'type' => 'Van',
            'brand' => 'Nissan',
            'model' => 'NV350 2024',
            'courier_id' => $courier5->id,
            'branch_id' => $mty->id,
            'is_active' => true,
        ]);

        $vehicle6 = VehicleModel::create([
            'plate_number' => 'PQR-1235',
            'type' => 'Motorcycle',
            'brand' => 'Suzuki',
            'model' => 'Address 2024',
            'courier_id' => $courier6->id,
            'branch_id' => $mty->id,
            'is_active' => true,
        ]);

        $pkg1 = PackageModel::create([
            'tracking_number' => 'AF-TEST-001',
            'description' => 'Dell XPS Laptop for urgent delivery',
            'weight' => 2.50,
            'status' => 'Registered',
            'branch_id' => $cdmx->id,
            'delivery_address' => 'Calle Madero 123, Col. Centro',
            'recipient_name' => 'Roberto Sánchez',
            'recipient_phone' => '55-4000-0001',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg1->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at CDMX Centro branch',
        ]);

        $pkg2 = PackageModel::create([
            'tracking_number' => 'AF-TEST-002',
            'description' => 'Legal documents box',
            'weight' => 1.20,
            'status' => 'In Transit',
            'branch_id' => $cdmx->id,
            'courier_id' => $courier1->id,
            'vehicle_id' => $vehicle1->id,
            'delivery_address' => 'Av. Juárez 456, Col. Centro',
            'recipient_name' => 'Fernanda Castillo',
            'recipient_phone' => '55-4000-0002',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg2->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at CDMX Centro branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg2->id,
            'previous_status' => 'Registered',
            'new_status' => 'In Transit',
            'comment' => 'En route to destination',
            'location' => 'Zona Rosa, CDMX',
        ]);

        $pkg3 = PackageModel::create([
            'tracking_number' => 'AF-TEST-003',
            'description' => 'Samsung 27" Monitor',
            'weight' => 5.80,
            'status' => 'Out for Delivery',
            'branch_id' => $gdl->id,
            'courier_id' => $courier3->id,
            'vehicle_id' => $vehicle3->id,
            'delivery_address' => 'Av. López Mateos 789, Zapopan',
            'recipient_name' => 'Miguel Ángel Torres',
            'recipient_phone' => '33-5000-0003',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg3->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at Guadalajara Norte branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg3->id,
            'previous_status' => 'Registered',
            'new_status' => 'In Transit',
            'comment' => 'Departing from branch',
            'location' => 'Zapopan Centro',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg3->id,
            'previous_status' => 'In Transit',
            'new_status' => 'Out for Delivery',
            'comment' => 'Courier on delivery route',
            'location' => 'Av. López Mateos, Zapopan',
        ]);

        $pkg4 = PackageModel::create([
            'tracking_number' => 'AF-TEST-004',
            'description' => 'iPhone 15 Pro',
            'weight' => 0.30,
            'status' => 'Delivered',
            'branch_id' => $mty->id,
            'courier_id' => $courier5->id,
            'vehicle_id' => $vehicle5->id,
            'delivery_address' => 'Calle Hidalgo 321, San Pedro',
            'recipient_name' => 'Patricia Valdez',
            'recipient_phone' => '81-6000-0004',
            'delivered_at' => now()->subHours(2),
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at Monterrey Sur branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => 'Registered',
            'new_status' => 'In Transit',
            'comment' => 'On delivery route',
            'location' => 'San Pedro, NL',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => 'In Transit',
            'new_status' => 'Out for Delivery',
            'comment' => 'Last mile delivery',
            'location' => 'Col. Del Valle, San Pedro',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => 'Out for Delivery',
            'new_status' => 'Delivered',
            'comment' => 'Delivered to recipient. Signed: Patricia Valdez',
            'location' => 'Calle Hidalgo 321, San Pedro',
        ]);

        $pkg5 = PackageModel::create([
            'tracking_number' => 'AF-TEST-005',
            'description' => 'Office supplies box',
            'weight' => 10.00,
            'status' => 'Cancelled',
            'branch_id' => $gdl->id,
            'delivery_address' => 'Calle Independencia 555, Guadalajara',
            'recipient_name' => 'XYZ Company S.A.',
            'recipient_phone' => '33-7000-0005',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg5->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at Guadalajara Norte branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg5->id,
            'previous_status' => 'Registered',
            'new_status' => 'Cancelled',
            'comment' => 'Cancelled at customer request',
        ]);
    }
}
