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
        // Users
        User::factory()->create([
            'name' => 'Admin Aeroflash',
            'email' => 'admin@aeroflash.com',
            'password' => 'password',
        ]);

        // Branch
        $ss = BranchModel::create([
            'name' => 'San Salvador',
            'address' => 'Colonia Escalon, San Salvador Centro',
            'phone' => '2410 0001',
        ]);

        $sa = BranchModel::create([
            'name' => 'Santa Ana',
            'address' => 'Centro de Santa Ana',
            'phone' => '2410 0002',
        ]);

        $sm = BranchModel::create([
            'name' => 'San Miguel',
            'address' => ' Av. Roosevelt, San Miguel',
            'phone' => '2410 0003',
        ]);

        // Courier
        $courier1 = CourierModel::create([
            'name' => 'Carlos López',
            'email' => 'carlos.lopez@aeroflash.com',
            'phone' => '7000 1231',
            'branch_id' => $ss->id,
            'is_active' => true,
        ]);

        $courier2 = CourierModel::create([
            'name' => 'María García',
            'email' => 'maria.garcia@aeroflash.com',
            'phone' => '7000 1232',
            'branch_id' => $ss->id,
            'is_active' => true,
        ]);

        $courier3 = CourierModel::create([
            'name' => 'Juan Hernández',
            'email' => 'juan.hernandez@aeroflash.com',
            'phone' => '7000 1233',
            'branch_id' => $sa->id,
            'is_active' => true,
        ]);

        $courier4 = CourierModel::create([
            'name' => 'Ana Martínez',
            'email' => 'ana.martinez@aeroflash.com',
            'phone' => '7000 1234',
            'branch_id' => $sa->id,
            'is_active' => true,
        ]);

        $courier5 = CourierModel::create([
            'name' => 'Pedro Ramírez',
            'email' => 'pedro.ramirez@aeroflash.com',
            'phone' => '7000 1235',
            'branch_id' => $sm->id,
            'is_active' => true,
        ]);

        $courier6 = CourierModel::create([
            'name' => 'Laura Díaz',
            'email' => 'laura.diaz@aeroflash.com',
            'phone' => '7000 1236',
            'branch_id' => $sm->id,
            'is_active' => true,
        ]);

        // Vehicle
        $vehicle1 = VehicleModel::create([
            'plate_number' => 'ABC-1234',
            'type' => 'Van',
            'brand' => 'Ford',
            'model' => 'Transit 2024',
            'courier_id' => $courier1->id,
            'branch_id' => $ss->id,
            'is_active' => true,
        ]);

        $vehicle2 = VehicleModel::create([
            'plate_number' => 'DEF-5678',
            'type' => 'Motorcycle',
            'brand' => 'Honda',
            'model' => 'Cargo 2024',
            'courier_id' => $courier2->id,
            'branch_id' => $ss->id,
            'is_active' => true,
        ]);

        $vehicle3 = VehicleModel::create([
            'plate_number' => 'GHI-9012',
            'type' => 'Van',
            'brand' => 'Chevrolet',
            'model' => 'Express 2024',
            'courier_id' => $courier3->id,
            'branch_id' => $sa->id,
            'is_active' => true,
        ]);

        $vehicle4 = VehicleModel::create([
            'plate_number' => 'JKL-3456',
            'type' => 'Motorcycle',
            'brand' => 'Yamaha',
            'model' => 'NMax 2024',
            'courier_id' => $courier4->id,
            'branch_id' => $sa->id,
            'is_active' => true,
        ]);

        $vehicle5 = VehicleModel::create([
            'plate_number' => 'MNO-7890',
            'type' => 'Van',
            'brand' => 'Nissan',
            'model' => 'NV350 2024',
            'courier_id' => $courier5->id,
            'branch_id' => $sm->id,
            'is_active' => true,
        ]);

        $vehicle6 = VehicleModel::create([
            'plate_number' => 'PQR-1235',
            'type' => 'Motorcycle',
            'brand' => 'Suzuki',
            'model' => 'Address 2024',
            'courier_id' => $courier6->id,
            'branch_id' => $sm->id,
            'is_active' => true,
        ]);

        // Package
        $pkg1 = PackageModel::create([
            'tracking_number' => 'AF-TEST-001',
            'description' => 'Dell XPS Laptop for urgent delivery',
            'weight' => 2.50,
            'status' => 'Registered',
            'branch_id' => $ss->id,
            'delivery_address' => 'Calle Madero 123, Col. Centro',
            'recipient_name' => 'Roberto Sánchez',
            'recipient_phone' => '4000 0001',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg1->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at San Salvador branch',
        ]);

        $pkg2 = PackageModel::create([
            'tracking_number' => 'AF-TEST-002',
            'description' => 'Legal documents box',
            'weight' => 1.20,
            'status' => 'In Transit',
            'branch_id' => $ss->id,
            'courier_id' => $courier1->id,
            'vehicle_id' => $vehicle1->id,
            'delivery_address' => 'Av. Juárez 456, Col. Centro',
            'recipient_name' => 'Fernanda Castillo',
            'recipient_phone' => '4000 0002',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg2->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at San Salvador branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg2->id,
            'previous_status' => 'Registered',
            'new_status' => 'In Transit',
            'comment' => 'En route to destination',
            'location' => 'Zona Rosa, San Salvador',
        ]);

        $pkg3 = PackageModel::create([
            'tracking_number' => 'AF-TEST-003',
            'description' => 'Samsung 27" Monitor',
            'weight' => 5.80,
            'status' => 'Out for Delivery',
            'branch_id' => $sa->id,
            'courier_id' => $courier3->id,
            'vehicle_id' => $vehicle3->id,
            'delivery_address' => 'Av. López Mateos 789, Santa Ana',
            'recipient_name' => 'Miguel Ángel Torres',
            'recipient_phone' => '5000 0003',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg3->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at Santa Ana branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg3->id,
            'previous_status' => 'Registered',
            'new_status' => 'In Transit',
            'comment' => 'Departing from branch',
            'location' => 'Santa Ana Centro',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg3->id,
            'previous_status' => 'In Transit',
            'new_status' => 'Out for Delivery',
            'comment' => 'Courier on delivery route',
            'location' => 'Av. López Mateos, Santa Ana',
        ]);

        $pkg4 = PackageModel::create([
            'tracking_number' => 'AF-TEST-004',
            'description' => 'iPhone 15 Pro',
            'weight' => 0.30,
            'status' => 'Delivered',
            'branch_id' => $sm->id,
            'courier_id' => $courier5->id,
            'vehicle_id' => $vehicle5->id,
            'delivery_address' => 'Calle Hidalgo 321, San Pedro',
            'recipient_name' => 'Patricia Valdez',
            'recipient_phone' => '6000 0004',
            'created_at' => now()->subDays(3),
            'delivered_at' => now()->subHours(2),
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at San Miguel branch',
            'created_at' => now()->subDays(3),
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => 'Registered',
            'new_status' => 'In Transit',
            'comment' => 'On delivery route',
            'location' => 'Colonia San Pedro, San Miguel',
            'created_at' => now()->subDays(2),
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => 'In Transit',
            'new_status' => 'Out for Delivery',
            'comment' => 'Last mile delivery',
            'location' => 'Col. Del Valle, San Miguel',
            'created_at' => now()->subDay(),
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg4->id,
            'previous_status' => 'Out for Delivery',
            'new_status' => 'Delivered',
            'comment' => 'Delivered to recipient. Signed: Patricia Valdez',
            'location' => 'Calle Hidalgo 321, San Miguel',
            'created_at' => now()->subHours(2),
        ]);

        $pkg5 = PackageModel::create([
            'tracking_number' => 'AF-TEST-005',
            'description' => 'Office supplies box',
            'weight' => 10.00,
            'status' => 'Cancelled',
            'branch_id' => $sa->id,
            'delivery_address' => 'Calle Independencia 555, Santa Ana',
            'recipient_name' => 'XYZ Company S.A.',
            'recipient_phone' => '7000 0005',
        ]);

        StatusHistoryModel::create([
            'package_id' => $pkg5->id,
            'previous_status' => null,
            'new_status' => 'Registered',
            'comment' => 'Package registered at Santa Ana branch',
        ]);
        StatusHistoryModel::create([
            'package_id' => $pkg5->id,
            'previous_status' => 'Registered',
            'new_status' => 'Cancelled',
            'comment' => 'Cancelled at customer request',
        ]);
    }
}
