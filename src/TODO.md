# TODO: Fix Duplicate Placa Error and Populate Sucursales Dropdown

## Completed Tasks
- [x] Edit `src/Vehiculos/procesar_vehiculo.php` to add try-catch around `$stmt->execute()` for handling duplicate placa errors gracefully.
- [x] Add INSERT statements to `scripts/database.sql` for missing sucursales: Zona 10 (Sede Principal), Zona 11 (Las Majadas), Zona 14, Condado Concepción, Dinamia Cayalá.
- [x] Run Docker command to insert sucursales data into the database.
- [x] Test vehicle creation to ensure error handling and full sucursales dropdown.
