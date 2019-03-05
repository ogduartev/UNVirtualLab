__UNVirtualLab__ es una aplicación web para gestionar laboratorios virtuales diseñados con _OpenModelica_. Está basado en una arquitertura LAMP.

# Instalación

## IN.1. Preparación de servidores:

	IN.1.1. Efectuar una instalación estándar del servidor mysql. En este proceso
	de instalación se define el nombre del usuario de administración y su clave
	de usuario. Archivar estos 	datos para usarlos en el paso 3.1.

	IN.1.2. Realizar una instalación estándar del servidor php.

	IN.1.3. Realizar una instalación estándar del servidor http apache 2 .

	IN.1.4. Configurar el servidor http apache:

		IN.1.4.1. Crear o seleccionar un directorio en el que se instalarán los
		scripts de UNVirtualLab. En el contexto de este manual, la ruta de este
		directorio se denomina DIRUNVL.

		IN.1.4.2. Configurar apache para que tenga acceso al directorio DIRUNVL. Si
		el directorio raíz de apache es /var/www esto se consigue con los siguientes
		pasos:

			IN.1.4.2.1. Crear un enlace simbólico:

				ln -s DIRUNVL /var/www/unvl

			IN.1.4.2.2. Editar el archivo sites-available/default, que usualmente está
			en el ditectorio en /etc/apache2/ para adicionar el directorio DIRUNVL y
			bloquear el acceso al archivo DIRUNVL/config. Esto se consigue adicionando
			los bloques:

				<Directory DIRUNVL/>
					Options Indexes FollowSymLinks MultiViews
					AllowOverride None
					Order allow,deny
					allow from all
				</Directory>

				<Directory DIRUNVL/config/>
					Allow from None
					Order allow,deny
				</Directory>

			IN.1.4.2.3. Reiniciar el servidor apache

				sudo /etc/init.d/apache2 restart

	IN.1.5. Si se desea realizar una instalación con compilación, es necesario
	instalar el compilador de OpenModelica:

		IN.1.5.1. Editar el archivo /etc/apt/sources.list para adicionar la línea:

			deb http://build.openmodelica.org/apt precise nightly

		IN.1.5.2. Desde una línea de comando actualizar el listado de repositorios:

			sudo apt-get update

		IN.1.5.3. Desde una línea de comando instalar el compilador:

			sudo apt-get install omc

	IN.1.6. Si se desea realizar una instalación para el modelador, la instrucción
	del paso 1.5.1. debe remplazarse por

		sudo apt-get install openmodelica

## IN.2. Configuración:
En este paso se instalan los scripts php de UNVirtualLab y se editan los
archivos de configuración. Los pasos a seguir son:

	IN.2.1. Copiar el archivo unvl.tar.gz en el directorio DIRUNVL

	IN.2.2. Descomprimir el archivo unvl.tar.gz

		cd DIRUNVL
		gunzip unvl.tar.gz
		tar -xf unvl.tar

	Como resultado, el archivo unvl.php debe estar ubicado como DIRUNVL/unvl.php

	IN.2.3. Editar el archivo de configuración DIRUNVL/config/unvlconfig.txt.

	IN.2.4. Asignar los permisos de lectura adecuados para el archivo de
	configuración. Con las siguientes instrucciones se otorga permiso de lectura
	sólo al usuario dueño del archivo.

		cd DIRUNVL/config
		chmod 444 unvlconfig.txt

## IN.3. Creación de la base de datos:
El script sql/crearSchema.php se encarga de crear la base de datos Mysql, los
usuarios y los permisos necesarios. Para ello es necesario conocer el nombre y
la clave de acceso MySql de un usuario con permisos de creación y gestión de
bases de datos (típicamente root). Por seguridad, esta información no debe
quedar disponible en ningún archivo del servidor. Los pasos a seguir son los
siguientes:

	IN.3.1. Editar el script sql/crearSchema.php. En las líneas 4 y 5 deben
	editarse las variables

		$username y $userpass para indicar cuál es el nombre de usuario MySql y
		clave de acceso con permisos de creación y gestión de bases de datos.
		Por ejemplo:

		$username="root";
		$userpass="rootpassword";

	IN.3.2. Correr el script sql/crearSchema.php.

		cd DIRUNVL/sql
		php crearSchema.php

	Este script lee la información de configuración del archivo
	config/unvlconfig.txt y realiza las siguientes tareas:

		i. Se conecta con el servidor de la base de datos DBserver usando el usuario
		y la clave	de acceso definidas en las variables $username y $userpass
		(archivo sql/crearSchema.php).
		ii. Crea una base de datos cuyo nombre está definido por la variable DBname
		(archivo	config/unvlconfig.txt).
		iii. Corre el script sql/unvl.sql que se encarga de crear el schema de la
		base de datos creada en el paso II.
		iv. Crea un usuario mysql cuyo nombre y clave de acceso están definidos por
		las variables DBuser y DBuserpass, respectivamente
		(archivo config/unvlconfig.txt).
		v. Le asigna al usuario creado en el paso IV permiso para leer registros
		(SELECT) de todas las tablas de la base de datos creada en el paso II.
		vi. Crea un usuario mysql cuyo nombre y clave de acceso están definidos por
		las variables DBadmin y DBadminpass, respectivamente
		(archivo config/unvlconfig.txt).
		vii. Le asigna al usuario creado en el paso VI permiso para leer, crear,
		editar y eliminar registros (SELECT, UPDATE, INSERT y DELETE) de todas las
		tablas de la base de datos creada en el paso II.
		viii. Crea un usuario UNVirtualLab con rol de administrador cuyo nombre y
		clave de acceso están difinos en las variables UNVLadmin y UNVLadminpass,
		respectivamente (archivo config/unvlconfig.txt).
		ix. Crea una sección de modelos de nombre unvl en la base de datos creada en
		el paso II.

	IN.3.3. Borrar el archivo sql/crearSchema.php.

		rm DIRUNVL/sql/crearSchema.php

## IN.4. Pruebas:
Las pruebas iniciales verifican que UNVirtualLab se encuentre disponible en la
red, y que el acceso al usuario administrador esté habilitado.

	IN.4.1. Utilizando un programa navegador web, cargar la dirección definida por
	la variable URLbase (archivo config/unvlconfig.txt). El mismo resultado	debe
	obtenerse si se carga la dirección seguida de unvl.php.

	IN.4.2. Utilizando un programa navegador web cargar la dirección definida por
	la variable URLbase seguida de admin.php.

	IN.4.3. A partir de la página del numeral anterior, iniciar una sesión
	utilizando como nombre	de usuario y clave de acceso las variables:

		UNVLadmin
		UNVLadminpass

	Estas variables están definidas en el archivo config/unvlconfig.txt.
	Seleccionar la única opción del menú de navegación (unvl), agregar una sección
	hija de nombre ‘Pruebas’ y seleccionarla en el menú.

	IN.4.4. Importar el modelo de prueba de un Motor DC:

		cd DIRUNVL/samples
		php importModel.php testExport.txt

	IN.4.5. En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Documentación (.pdf)’, seleccionar el
	archivo DIRUNVL/samples/MotorDC.pdf y enviarlo.

IN.4.6. Pruebas de simulación

*	__Para una instalación mínima__:

	IN.4.6.1 En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Ejecutable (.tar.gz)’, seleccionar y
	enviar el archivo:

		DIRUNVL/samples/Min/DCmotorMinimal.tar.gz

	IN.4.6.2 En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Modelica (.tar.gz)’, seleccionar y
	enviar el archivo

		DIRUNVL/samples/Min/DCmotorMinimalSource.tar.gz

*	__Para una instalación con compilación__:

	IN.4.6.1 En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Ejecutable (.tar.gz)’, seleccionar y
	enviar el archivo

		DIRUNVL/samples/Compila/DCmotorCompila.tar.gz

	IN.4.6.2 En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Modelica (.tar.gz)’, seleccionar y
	enviar el archivo

		DIRUNVL/samples/Compila/DCmotorCompilaSource.tar.gz

* __Para una instalación para modelador__:

	IN.4.6.1 Utilizando OMShell compilar el modelo del archivo DIRUNVL/samples/
	Modelador/DCMotor.mo y simular el modelo con salida en formato csv con las
	siguientes instrucciones:

		loadModel(Modelica)
		cd DIRUNVL/samples/Modelador
		loadFile("DCmotor.mo");
		simulate(DCmotor, startTime=0, stopTime=10,outputFormat="csv");

	IN.4.6.2 Crear un archivo comprimido en formato .tar.gz con los archivos:

		DCmotor
		DCmotor_init.xml
		DCmotor_res.csv

	Estos archivos son creados por OMShell como parte del proceso de simulación.
	Para efectos de esta explicación, se supondrá que el nombre dado al archivo
	comprimido es DCmotorModelador.tar.gz

	IN.4.6.3 Crear un archivo comprimido en formato .tar.gz con el archivos
	DCmotor.mo. Para efectos de esta explicación, se supondrá que el nombre dado
	al archivo comprimido es: DCmotorModeladorSource.tar.gz

	IN.4.6.4 En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Ejecutable (.tar.gz)’, seleccionar y
	enviar el archivo

		DIRUNVL/samples/Modelador/DCmotorModelador.tar.gz

	IN.4.6.5 En la página de edición del modelo ‘Motor DC’, hacer click sobre el
	ícono que está a la derecha del letrero ‘Modelica (.tar.gz)’, seleccionar y
	enviar el archivo

		DIRUNVL/samples/Modelador/DCmotorModeladorSource.tar.gz

	IN.4.7. Utilizando un programa navegador web, cargar la dirección definida por
	la variable URLbase (archivo config/unvlconfig.txt) y seleccionar el modelo
	‘Motor DC’.

	IN.4.8. Modificar el valor del parámetro ‘Resistencia’ con la interfaz, y
	ordenar una simulación.
