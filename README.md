The **Streamer** extension displays information about streamers from various streaming services.  It can display information as simple as online/offline to as extensive as the current thumbnail.

* **Project Homepage:** [Documentation at Github](https://github.com/CurseStaff/Streamer)
* **Mediawiki Extension Page:** [Extension:Streamer](https://www.mediawiki.org/wiki/Extension:Streamer)
* **Source Code:** [Source Code at Github](https://github.com/CurseStaff/Streamer)
* **Bugs:** [Issue Tracker at Github](https://github.com/CurseStaff/Streamer/issues)
* **Licensing:** Streamer is released under [The GNU Lesser General Public License, version 3.0](http://opensource.org/licenses/lgpl-3.0.html).


#Installation

Download and place the file(s) in a directory called Streamer in your extensions/ folder.

Add the following code at the bottom of your LocalSettings.php:

	require_once("$IP/extensions/Streamer/Streamer.php");

Done! Navigate to "Special:Version" on your wiki to verify that the extension is successfully installed.

#Usage

![](documentation/BasicInterface.png)

##Tags

###\#streamer - Parser Tag
The #streamer tag format accepts X and Y coordinate positions to select a section of the image from a traditional column and row format.

Basic Syntax:


	{{#streamer:
	service=[Service]
	|user=[User]
	|show=[Comma Delimited List of Items to Show]
	|template=[Template to Use]
	}}

####Attributes for #sprite Tag

|       Attribute       | Description                                                                                                                                                                                   |
|----------------------:|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| service               | **Required**: yes<br/>The file page containing the image to use.                                                                                                                              |
| user                  | **Required**: yes<br/>The user identifier for user on the streaming service.                                                                                                                  |
| show                  | **Required**: yes, **Options**: user, status, viewers, thumbnail<br/>Comma delimited list of items to display in the template.                                                                |
| template              | **Required**: no, **Default**: default, **Built In**: status, viewers, thumbnail, preview<br/>Use a built in display template or specify a template to use.  Example: Template:Streamer_Thumb |

####Example

To display online/offline status of TwitchPlaysPokemon from the Twitch streaming service:

	{{#streamer:
	service=Twitch
	|user=twitchplayspokemon
	|show=status
	|template=default
	}}

![](documentation/TwitchPlaysPokemonExample.png)