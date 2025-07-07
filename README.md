# Filament Nested Comments & Emoji Reactions

[![Latest Version on Packagist](https://img.shields.io/packagist/v/coolsam/nested-comments.svg?style=flat-square)](https://packagist.org/packages/coolsam/nested-comments)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/coolsam726/nested-comments/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/coolsam726/nested-comments/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/coolsam726/nested-comments/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/coolsam726/nested-comments/actions?query=workflow%3A"Fix+PHP+Code+Styling"+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/actions/workflow/status/coolsam726/nested-comments/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/coolsam726/nested-comments/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/coolsam/nested-comments.svg?style=flat-square)](https://packagist.org/packages/coolsam/nested-comments)


This package allows you to incorporate comments and replies in your Filament forms, infolists, pages, widgets etc, or even simply in your livewire components. Comment replies can be nested as deep as you want, using the Nested Set data structure. Additionally, the package comes with a Reactions feature to enable your users to react to any of your models (e.g comments or posts) with selected emoji reactions.

![image](https://github.com/user-attachments/assets/2900e2a4-9ad2-40e2-8819-2650b6d70803)

## Installation

You can install the package via composer:

```bash
composer require coolsam/nested-comments
```

Run the installation command and follow the prompts:

```bash
php artisan nested-comments:install
```
During the installation, you will be asked if you would like to publish and replace the config file.
This is important especially if you are upgrading the package to a newer version in which the config file structure has changed.
No worries, if you have customizations in your config file that you would like to keep, your current config file will be backed up to `config/nested-comments.php.bak` before the new config file is published.

You will also be asked if you would like to re-publish the package's assets. This is also important in case the package's styles and scripts have changed in the new version.

Adjust the configuration file as necessary, then run migrations.

`That's it! You are now ready to add nested comments

## Usage: Comments
At the very basic level, this package is simply a Livewire Component that takes in a model record which is commentable. Follow the following steps to prepare your model to be commentable or reactable:
1. Add the `HasComments` trait to your model
```php

use Coolsam\NestedComments\Concerns\HasComments;

class Conference extends Model
{
    use HasComments;

    // ...
}

```
2. If you would like to be able to react to your model directly as well, add the `HasReactions` trait to your model
```php
use Coolsam\NestedComments\Concerns\HasReactions;

class Conference extends Model
{
    use HasReactions;

    // ...
}
```
3. You can now access the comments and reactions of your model in the following ways

### Using the Comments Infolist Entry

```php
public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Basic Details')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('start_date')
                            ->dateTime(),
                        TextEntry::make('end_date')
                            ->dateTime(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ]),
                
                // Add the comments entry
                \Coolsam\NestedComments\Filament\Infolists\CommentsEntry::make('comments'),
            ]);
    }
```
![image](https://github.com/user-attachments/assets/da84b49e-66c7-4453-b5d4-b7b18f204bba)



### Using the Comments Widget inside a Resource Page (e.g EditRecord)

As long as the resource page interacts with the record, the CommentsWidget will resolve the record automatically.

```php
class EditConference extends EditRecord
{
    protected static string $resource = ConferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            \Coolsam\NestedComments\Filament\Widgets\CommentsWidget::class,
        ];
    }
}
```
![image](https://github.com/user-attachments/assets/bd56d52d-b791-4f24-a202-b0948574d811)

### Using the Comments Widget in a custom Filament Page (You have to pass $record manually)

```php
// NOTE: It's up to you how to get your record, as long as you pass it to the widget
public function getRecord(): ?Conference
{
    return Conference::latest()->first();
}

protected function getFooterWidgets(): array
{
    return [
        CommentsWidget::make(['record' => $this->getRecord()])
    ];
}
```

### Using the Comments Page Action in a Resource Page (which interacts with $record)

```php
namespace App\Filament\Resources\ConferenceResource\Pages;

use App\Filament\Resources\ConferenceResource;
use Coolsam\NestedComments\Filament\Actions\CommentsAction;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewConference extends ViewRecord
{
    protected static string $resource = ConferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make()
                ->badgeColor('danger')
                ->badge(fn(Model $record) => $record->getAttribute('comments_count')),
            Actions\EditAction::make(),
        ];
    }
}
```
![image](https://github.com/user-attachments/assets/678d3f1e-b3f9-4a77-b263-af5538c72e2b)

![image](https://github.com/user-attachments/assets/372c6390-ea4e-4d19-8943-784506126cc1)

### Using the Comments Page Action in a custom Filament Page (You have to pass $record manually)
In this case you will have to pass the record attribute manually.

```php
protected function getHeaderActions(): array
{
    return [
        CommentsAction::make()
            ->record($this->getRecord()) // Define the logic for getting your record e.g in $this->getRecord()
            ->badgeColor('danger')
            ->badge(fn(Model $record) => $record->getAttribute('comments_count')),
        Actions\EditAction::make(),
    ];
}
```

### Using the Comments Table Action

```php
public static function table(Table $table): Table
{
    return $table
        ->columns([
            // ... Columns
        ])
        ->actions([
            \Coolsam\NestedComments\Filament\Tables\Actions\CommentsAction::make()
                ->button()
                ->badgeColor('danger')
                ->badge(fn(Conference $record) => $record->getAttribute('comments_count')),
            // ... Other actions
        ]);
}
```
![image](https://github.com/user-attachments/assets/27eead51-c237-4865-b185-3245629cabe4)

### Using the Comments Blade Component ANYWHERE!
This unlocks incredible possibilities. It allows you to render your comments even in your own frontend blade page. All you have to do is simply pass the commentable `$record` to the blade component
```php
$record = Conference::find(1); // Get your record from the database then,

<x-nested-comments::comments :record="$record"/>
```

Alternatively, you could use the Livewire component if you prefer.
```php
$record = Conference::find(1); // Get your record from the database then,

<livewire:nested-comments::comments :record="$record"/>
```

### Mentions
The package uses Filament TipTap Editor which supports mentions. You can mention users in your comments by typing `@` followed by the user's name.
In the future, the package will support sending notifications to the mentioned users via database notifications if supported.
For more on how to customize the mentions, see the [Package Customization](#customize-how-to-get-the-mention-items) section below.

![image](https://github.com/user-attachments/assets/bd7a395a-fc32-4057-b6bc-24763132f555)


## Usage: Emoji Reactions
This package also allows you to add emoji reactions to your models. You can use the `HasReactions` trait to add reactions to any model. The reactions are stored in a separate table, and you can customize the reactions that are available via the configuration file.
The Comments model that powers the comments feature described above already uses emoji reactions.

In order to start using reactions for your model, add the `HasReactions` trait to your model. You can then use the `reactions` method to get the reactions for the model.

```php
use Coolsam\NestedComments\Concerns\HasReactions;

class Conference extends Model
{
    use HasReactions;

    // ...
}
```
The above trait adds the `react()` method to your model, allowing you to toggle a reaction for the model. You can also use the `reactions` method to get the reactions for the model.

```php
$conference = Conference::find(1);
$comference->react('👍'); // React to the conference with a thumbs up emoji
```
You can also use the `reactions` method to get the reactions for the model.

```php
$conference = Conference::find(1);
$reactions = $conference->reactions; // Get the reactions for the conference
```
Other useful methods include
```php
/**
* @var \Illuminate\Database\Eloquent\Model&\Coolsam\NestedComments\Concerns\HasReactions $conference
 */
$conference = Conference::find(1);
$conference->total_reactions; // Get the total number of reactions for the conference
$conference->reactions_counts; // Get the no of reactions for each emoji for the model
$conference->my_reactions; // Get the reactions for the current user
$conference->emoji_reactors // Get the list of users who reacted to the model, grouped by emoji
$conference->isAllowed('👍') // check if the app allows the user to react with the specified emoji
$conference->reactions_map // return the map of all the reactions for the model, grouped by emoji. This tells you the number of reactions for each emoji, and whether the current user has reacted with that emoji
```
To interact with the methods above with ease within and even outside Filament, this package comes with the following handy components:

### Reactions Infolist Entry
```php
use Coolsam\NestedComments\Filament\Infolists\ReactionsEntry;

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            Section::make('Basic Details')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('start_date')
                        ->dateTime(),
                    TextEntry::make('end_date')
                        ->dateTime(),
                    TextEntry::make('created_at')
                        ->dateTime(),
                        // Add the reactions entry
                    ReactionsEntry::make('reactions')->columnSpanFull(),
                ])->columns(4),
        ]);
}
```
![image](https://github.com/user-attachments/assets/06ae7e76-1668-4e92-9a4f-a125f7d94b03)

### Reactions Blade Component
Just include the blade component anywhere in your blade file and pass the model record to it.
```php
$record = Conference::find(1); // Get your record from the database then,
```
In your view:
```bladehtml
<x-nested-comments::reactions :record="$record"/>
```

### Reactions Livewire Component
Similar to the blade component, you can use the Livewire component anywhere in your Livewire component and pass the model record to it.
```php
$record = Conference::find(1); // Get your record from the database then,
```
In your view:
```bladehtml
<livewire:nested-comments::reaction-panel :record="$record"/>
```
The two components can be used anywhere, in resource pages, custom pages, actions, form fields, widgets, livewire components or just plain blade views. Here is a sample screenshot of how the components will be rendered:
![image](https://github.com/user-attachments/assets/0162f294-0477-454c-ae5c-67424edc207f)


## Package Customization
You can customize the package by changing most of the default values in the config file after publishing it.
Additionally, you can customize how the package interacts with your models by overriding some methods in your commentable model.

### Customize how to get the Comment Author's Name
You can customize how to get the comment author's name by overriding the `getUserName` method in your commentable model.
By default, the package uses the `name` attribute of the user model, but you can change this to any other attribute or method that returns a string.

This name will be displayed in the comment card, and it will also be used to mention the user in the comment text.

```php
// e.g in your Post model or any other model that uses the HasComments trait
use Coolsam\NestedComments\Concerns\HasComments;

public function getUserName(Model|Authenticatable|null $user): string
{
    return $user?->getAttribute('username') ?? $user?->getAttribute('guest_name') ?? 'Guest';
}
```

### Customize the User's Avatar
You can customize the user's avatar by overriding the `getUserAvatar` method in your commentable model.

By default, the package uses [ui-avatars](https://ui-avatars.com) to generate the avatar based on the user's name, but you can change this to any other method that returns a URL to the user's avatar image.

```php
// e.g in your Post model or any other model that uses the HasComments trait
use Coolsam\NestedComments\Concerns\HasComments;

public function getUserAvatar(Model|Authenticatable|string|null $user): ?string
{
//    return 'https://yourprofile.url.png';
    return $user->getAttribute('profile_url') // get your user's profile url here, assuming you have defined it in your user's model.
}
```

### Customize how to get the Mention Items
You can customize how to get the mention items by overriding and changing the `getMentionsQuery` method in your commentable model.
By default, the package gets mention items from all users in your database. 
For example, if you would only like to mention users who have commented on the current thread, you can do so by changing the method to return only those users.
There is a handy method included in the default class to achieve this. Alternatively, you can go wild and mention fruits instead of users! The choice is within your freedom.

```php
// e.g in your Post model or any other model that uses the HasComments trait
use Coolsam\NestedComments\Concerns\HasComments;

public function getMentionsQuery(string $query): Builder
{
    return app(NestedComments::class)->getCurrentThreadUsersQuery($query, $this);
}
```

### Customize the Supported Emoji Reactions
You can customize the supported emoji reactions by changing the `reactions` array in the config file.
Decent defaults are provided, but you can change them to any emojis you prefer.

```php
return [
    '👍',
    '❤️',
    '😂',
    '😮',
    '😢',
    '😡',
];
```

### Customize Slide-over dialog
You can customize the display of slide-over dialog by changing many parameters in config file.

#### Customize display created date of comment
Set as your preference the display format created date below username of a comment
```php
'format-created-date' => 'F j Y h:i:s A',
```

#### Showing Header Section
Show the title section
```php
'show-heading' => true,
```
Show the refresh button
```php
'show-refresh-button' => true,
```
> [!NOTE]  
> Combining `'show-heading' => false` with `'show-refresh-button' => false` remove header's section completely.


#### Customization Badge Counter
Show the badge counter
```php
'show-badge-counter' => true,
```
Customize color
```php
'badge-counter-color' => 'info',
// 'danger', 'gray', 'info', 'success' or 'warning'
```

#### Customization Refresh button
Customize trigger style
```php
'style-refresh-button' => 'button', 
// 'button' or 'icon',
```
Customize icon
```php
'icon-refresh-button' => 'heroicon-m-sparkles',
```

Customize color
```php
'color-refresh-button' => 'info', 
// 'danger', 'gray', 'info', 'success' or 'warning'
```

Outlined button style
```php
'outlined-refresh-button' => false,
```
## Testing

```bash
composer test
```

## Open Source Dependencies

This package uses the following awesome open source packages, among many others under the hood:

* [Filament](https://filamentphp.com/)
* [Livewire](https://livewire.laravel.com/)
* [Laravel](https://laravel.com/)
* [AlpineJS](https://alpinejs.dev/)
* [Laravel NestedSet](https://github.com/lazychaser/laravel-nestedset)
* [Filament Tiptap Editor](https://github.com/awcodes/filament-tiptap-editor)

I am grateful for the work that has been put into these packages. They have made it possible to build this package in a short time.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sam Maosa](https://github.com/coolsam726)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
