<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        $productos_base = [
            'Tornillo','Tuerca','Arandela','Clavo','Perno','Remache','Taquete','Varilla','Alambre','Cable',
            'Manguera','Tubo PVC','Codo PVC','Unión PVC','Reducción PVC','Llave de paso','Válvula','Bomba',
            'Interruptor','Toma corriente','Breaker','Cable eléctrico','Foco LED','Reflector','Extension',
            'Pintura','Sellador','Masilla','Impermeabilizante','Pegante','Silicon','Limpiador',
            'Lija','Brocha','Rodillo','Espátula','Palustro','Nivel','Metro','Cinta métrica',
            'Destornillador','Martillo','Alicate','Llave inglesa','Llave fija','Bisturí','Serrucho',
            'Taladro','Broca','Disco de corte','Pulidora','Compresor','Pistola de calor',
            'Cemento','Arena','Gravilla','Cal','Yeso','Estuco','Pañete',
            'Ladrillo','Bloque','Teja','Perfil metálico','Ángulo','Canal','Platina',
            'Bisagra','Cerradura','Manija','Chapa','Pasador','Candado',
            'Malla','Alambre de púas','Puntilla','Grapas','Abrazadera',
            'Cinta aislante','Cinta teflon','Cinta doble faz','Silicona',
            'Guante','Casco','Gafas de seguridad','Tapa oídos','Overol','Botas',
            'Carretilla','Balde','Pala','Pica','Rastrillo','Escoba','Trapeador',
        ];

        $materiales = ['Galvanizado','Inoxidable','PVC','Hierro','Aluminio','Plástico','Madera','Caucho','Cobre','Zinc'];
        $tamaños = ['1/4"','1/2"','3/4"','1"','2"','3"','4"','6"','8"','10mm','12mm','16mm','20mm','25mm','50mm'];

        $unidades = [
            'Unidad','Par','Docena','Caja','Paquete','Sobre','Frasco','Botella','Lata','Tubo',
            'Gramo','Kilogramo','Libra','Onza','Mililitro','Litro','Galón',
            'Milímetro','Centímetro','Metro','Metro lineal','Pulgada','Pie','Metro cuadrado'
        ];

        $unidades_por_tipo = [
            'Tornillo'=>'Unidad','Tuerca'=>'Unidad','Arandela'=>'Unidad','Clavo'=>'Libra',
            'Perno'=>'Unidad','Remache'=>'Unidad','Taquete'=>'Unidad','Varilla'=>'Metro',
            'Alambre'=>'Kilogramo','Cable'=>'Metro','Manguera'=>'Metro','Tubo PVC'=>'Metro',
            'Cemento'=>'Bulto','Arena'=>'Metro cúbico','Gravilla'=>'Metro cúbico',
            'Pintura'=>'Galón','Sellador'=>'Frasco','Masilla'=>'Kilogramo',
            'Lija'=>'Unidad','Brocha'=>'Unidad','Rodillo'=>'Unidad',
        ];

        $productos = [];
        $codigos = [];
        $contador = 1;

        while (count($productos) < 1000) {
            $base = $productos_base[array_rand($productos_base)];
            $material = rand(0, 1) ? ' ' . $materiales[array_rand($materiales)] : '';
            $tamaño = rand(0, 1) ? ' ' . $tamaños[array_rand($tamaños)] : '';
            $nombre = $base . $material . $tamaño . ' #' . $contador;

            $unidad = $unidades_por_tipo[$base] ?? $unidades[array_rand($unidades)];

            $precio_compra = rand(500, 150000);
            $precio_venta  = (int)($precio_compra * (1 + rand(15, 60) / 100));
            $iva           = rand(0, 1) ? 19 : 0;
            $precio_con_iva = (int)($precio_venta * (1 + $iva / 100));

            $codigo = 'PROD' . str_pad($contador, 6, '0', STR_PAD_LEFT);

            $productos[] = [
                'nombre'        => $nombre,
                'codigo_barras' => $codigo,
                'precio_compra' => $precio_compra,
                'precio_venta'  => $precio_venta,
                'iva'           => $iva,
                'precio_con_iva'=> $precio_con_iva,
                'stock'         => rand(0, 500),
                'unidad'        => $unidad,
                'activo'        => 1,
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            $contador++;
        }

        DB::table('productos')->insert($productos);
    }
}