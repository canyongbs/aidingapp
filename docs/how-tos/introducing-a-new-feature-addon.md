# Introducing a New Feature Addon

This guide covers gating features behind external activation.

---

## Applying restrictions

### Restricting Filament Resources

A feature gated model's policy will often be sufficient at correctly hiding and displaying its related resource. To do so, use the `PerformsFeatureChecks` trait in the policy and reference the `Features` enum.

```php
use App\Concerns\PerformsFeatureChecks;
use App\Enums\Feature;
use App\Models\Authenticatable;

class ExamplePolicy
{
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasFeatures())) {
            return $response;
        }

        return null;
    }

    // ...

    /**
     * @return array<Feature>
     */
    protected function requiredFeatures(): array
    {
        return [Feature::ExampleFeature];
    }
}
```

### Other Filament Pages

Some pages may need a more explicit check in their `canAccess` method. Make sure to check all pages relating to the feature in question and verify that they are properly restricted.

```php
use App\Enums\Feature;
use Illuminate\Support\Facades\Gate;

public static function canAccess(): bool
    {
        if (! Gate::check(Feature::ExampleFeature->getGateName())) {
            return false;
        }

        //...
    }
```

### Other Changes

Several other files will need to be updated as well.

`app/DataTransferObjects/LicenseManagement/LicenseAddonsData.php`: The constructor will need to have the new key added:

```php
public function __construct(
        // ...
        public bool $exampleFeature = false,
    ) {}
```

`app/Enums/Feature.php`: A new case will need to be added to the enum:

```php
case ExampleFeature = 'example-feature';
```

`app/Filament/Pages/ManageLicenseSettings.php`: A new Toggle will need to be added to the `Enabled Features` Section:

```php
Section::make('Enabled Features')
    ->columns()
    ->schema(
        [
            // ...
            Toggle::make('data.addons.exampleFeature')
                ->label('Example Feature'),
        ]
    ),
```

`tests/TestCase.php`: Both the `createTenant` function and the `refreshTenantTestingEnvironment` will need to be updated:

```php
new LicenseAddonsData(
        // ...
        exampleFeature: true,
    )
```

### Tests

Additionally, access control tests should be modified or created for the affected pages and/or resources (one page in a resource, e.g. the list page, is acceptable; but make sure to seprately test relationship managers in other resources).

```php
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->exampleFeature = false;
    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('example.view-any');

    actingAs($user);

    get(ListExamples::getUrl())->assertForbidden();

    $settings->data->addons->exampleFeature = true;
    $settings->save();

    $user->revokePermissionTo('example.view-any');

    get(ListExamples::getUrl())->assertForbidden();

    $user->givePermissionTo('example.view-any');

    get(ListExamples::getUrl())->assertSuccessful();
});
```

---

## Listening For Features

Because this feature is now toggled externally, the rules for requests relating to tenants will also need to be updated to listen for this feature.

`app/Http/Requests/Tenants/CreateTenantRequest.php` and `app/Http/Requests/Tenants/SyncTenantRequest.php` will both need an additional rule in their `rules()` functions:

```php
    public function rules(): array
    {
        return [
            // ...
            'addons.exampleFeature' => ['required', 'boolean'],
        ];
    }
```

---

## Additional Information

Changes will also need to be made to Olympus when features are externally gated, as that gating is controlled by Olympus tenant creation. For information on that process, please view [the documentation regarding that here](https://github.com/canyongbs/olympus/blob/main/docs/how-tos/adding-a-new-product-feature-addon.md).
