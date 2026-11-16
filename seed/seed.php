<?php

declare(strict_types=1);

/**
 * Seeds the shared teme-dev site with demo stories for Fable's screenshots and audit. DEV TOOL ONLY: it is not part
 * of the theme and is never shipped. It reaches into Teme's own Operations because an outside developer has no
 * public way to put content, menus and regions into a site except the admin (which needs a sign-in) or the
 * management API (which needs a key); that is recorded as finding #13 in FINDINGS.md. The Operations and their
 * inputs are used exactly as the temecms theme's seed script uses them.
 *
 * Run from the teme-dev folder, and only while holding the dev-site lock (teme-plugins/.teme-dev.lock):
 *
 *   php ../teme-themes/egberipiribo/seed/seed.php media content menus regions
 *   php ../teme-themes/egberipiribo/seed/seed.php activate        (makes egberipiribo the site's theme)
 *   php ../teme-themes/egberipiribo/seed/seed.php restore temecms (puts another theme back)
 *
 * Everything it makes has a slug starting `fable-demo-`, and what it made is listed in seed/.state.json. To take
 * the demo content away, trash the posts whose address starts `fable-demo-` in the admin (Posts, search `fable-demo`).
 *
 * @package   egberipiribo
 * @since     0.1.0
 * @copyright Krafty Sprouts Media, LLC
 * @license   MIT
 */

use App\Domain\Contents\Models\Content;
use App\Domain\Contents\Operations\CreateDraft;
use App\Domain\Contents\Operations\Input\CreateDraftInput;
use App\Domain\Contents\Operations\Input\PublishRevisionInput;
use App\Domain\Contents\Operations\Input\SaveDraftInput;
use App\Domain\Contents\Operations\Input\UpdateContentDetailsInput;
use App\Domain\Contents\Operations\PublishRevision;
use App\Domain\Contents\Operations\SaveDraft;
use App\Domain\Contents\Operations\UpdateContentDetails;
use App\Domain\Design\Operations\ActivateTheme;
use App\Domain\Design\Operations\Input\ActivateThemeInput;
use App\Domain\Media\Operations\Input\UpdateMediaDetailsInput;
use App\Domain\Media\Operations\Input\UploadMediaInput;
use App\Domain\Media\Operations\UpdateMediaDetails;
use App\Domain\Media\Operations\UploadMedia;
use App\Domain\Menus\Operations\BindNavigationSlot;
use App\Domain\Menus\Operations\CreateNavigation;
use App\Domain\Menus\Operations\Input\BindNavigationSlotInput;
use App\Domain\Menus\Operations\Input\CreateNavigationInput;
use App\Domain\Menus\Operations\Input\SaveNavigationItemsInput;
use App\Domain\Menus\Operations\SaveNavigationItems;
use App\Domain\Regions\Operations\CreateRegion;
use App\Domain\Regions\Operations\Input\CreateRegionInput;
use Teme\Testing\Acting;
use Teme\Testing\Person;

$teme = getcwd();
require $teme.'/vendor/autoload.php';
$app = require $teme.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$steps = array_slice($argv, 1);
$statePath = __DIR__.'/.state.json';
$state = is_file($statePath) ? json_decode((string) file_get_contents($statePath), true) : [];
$state += ['media' => [], 'content' => [], 'regions' => []];
$save = static function () use (&$state, $statePath): void {
    file_put_contents($statePath, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
};
$say = static function (string $line): void {
    fwrite(STDOUT, $line."\n");
};

$admin = \App\Models\User::query()->where('email', 'admin@example.test')->first() ?? \App\Models\User::query()->orderBy('id')->firstOrFail();
$actor = new Person($admin->id, $admin->name, $admin->email, 'administrator');
$as = static fn (Closure $step, string $what): mixed => (new Acting)->as($actor, $step, $what);
$aid = (string) $admin->id;

/** Field keys from docs/reference/fields.md (the Post type). */
const F_EXCERPT = '01K5Z0F1ED00000000000000E3';
const F_IMAGE = '01K5Z0F1ED00000000000000F6';
const F_STICKY = '01K5Z0F1ED00000000000000P8';

$stories = [
    ['cats', 'What Is Your Cat Thinking? The Inner Life Cats Have Always Kept From Us', 'They have lived beside us for ten thousand years. New research suggests we have barely scratched the surface of who they are.', 1, true],
    ['wolves', 'The Alpha Wolf Was a Mistake. What Packs Really Teach Us About Leadership and Trust', 'The scientist who made the idea famous has spent twenty years taking it back. What replaced it is more useful.', 2, true],
    ['repaint', 'One Weekend, One Room: The Repainting Rule Decorators Swear By', 'Empty it on Friday night, cut in on Saturday, roll on Sunday. The order matters more than the paint.', 3, true],
    ['cutting', 'How to Grow a Cutting Garden That Flowers in Every Season', 'Twelve plants, sown in four batches, will keep a vase full from March to the first frost.', 4, true],
    ['pantry', 'The Pantry Principle: How to Cook Dinner From What You Already Have', 'Start with the starch, add something sharp, finish with fat. A method, not a recipe.', 5, false],
    ['flea', 'Natural Flea Prevention for Dogs: The Methods Vets Say Actually Work', 'Combing and hot washes hold up. Most essential oils do not, and some are dangerous.', 6, false],
    ['gallery', 'How to Hang a Gallery Wall That Does Not Look Like an Accident', 'Lay it out on the floor first, keep the gaps equal and hang the centre at eye level.', 7, false],
    ['dolomites', 'The Hidden Villages of the Dolomites: A Six-Day Walking Route Without a Car', 'Six valleys, five mountain huts and one bus timetable you will need to memorise.', 8, false],
    ['ferment', 'How to Start a Ferment Shelf: Five Beginner Jars for a Living Pantry', 'Salt, vegetables and time. Begin with sauerkraut and work up to hot sauce.', 1, false],
    ['thermostat', 'Smart Thermostats, Two Years On: What Saved Us Money and What Did Not', 'The schedule paid for itself in fourteen months. The room sensors never did.', 2, false],
    ['weeds', '12 Garden Weeds Worth Keeping, and the Ones to Pull on Sight', 'Clover feeds the soil and dandelions feed the bees. Bindweed feeds nothing but itself.', 3, false],
    ['lunch', 'Why You Should Eat Lunch Outside, Even in the Middle of Winter', 'Twenty minutes of daylight before 2 p.m. does more for sleep than most supplements.', 4, false],
    ['dogwalk', 'What Your Dog’s Walk Is Really Telling You, According to Behaviourists', 'The sniffing is not stalling. It is how a dog reads the neighbourhood.', 5, false],
    ['trains', 'Slow Trains Through the Highlands: Four Scenic Routes Worth the Fare', 'Sit on the left going north, book the 10.21 and bring your own sandwiches.', 6, false],
    ['apps', 'The Nine Apps That Still Earn a Place on Our Home Screens', 'We deleted forty. These are the ones we reinstalled within a week.', 7, false],
    ['bedtime', 'A 20-Minute Bedtime Ritual That Actually Sticks', 'Dim the lights, write tomorrow’s list and leave the phone in another room.', 8, false],
];

$text = static fn (string $t): array => ['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => $t]]];
$head = static fn (string $t): array => ['type' => 'heading', 'attrs' => ['level' => 2, 'id' => strtoupper((string) Illuminate\Support\Str::ulid())], 'content' => [['type' => 'text', 'text' => $t]]];
$body = static function (string $deck) use ($text, $head): array {
    return ['type' => 'doc', 'content' => [
        $text($deck.' This is demo text, written to show how a long read sets on the page: the measure, the rhythm of the paragraphs and the way a heading breaks them.'),
        $text('Good reporting starts with a plain question. What happened, who was there, and what would change if the answer were different? The rest is patience, and a willingness to be told that the first idea was wrong.'),
        $head('What the evidence says'),
        $text('The strongest findings come from long observation rather than single experiments. Researchers who followed the same households for years noticed patterns no short study could catch, and they were careful to say where their data ran out.'),
        ['type' => 'blockquote', 'content' => [$text('The method is simple. The discipline is doing it every day, including the days you would rather not.')]],
        $text('That discipline is the part most guides leave out. A tip is easy to read and hard to keep, so the useful ones are small enough to survive a bad week.'),
        $head('How to start'),
        ['type' => 'bulletList', 'content' => [
            ['type' => 'listItem', 'content' => [$text('Pick one change and give it two weeks.')]],
            ['type' => 'listItem', 'content' => [$text('Write down what happened, not what you hoped would happen.')]],
            ['type' => 'listItem', 'content' => [$text('Keep what works and drop the rest without guilt.')]],
        ]],
        $text('If a step feels like a chore, make it smaller. Almost everything worth doing can be done badly first, and improved once it is a habit.'),
        $head('What to do next'),
        $text('Start tonight. Come back to this story in a month and see what stuck.'),
        $text('Demo content: the people, places and findings here are invented for the theme’s screenshots.'),
    ]];
};

if (in_array('media', $steps, true)) {
    foreach (range(1, 8) as $n) {
        if (isset($state['media'][$n])) {
            continue;
        }
        $item = $as(fn () => app(UploadMedia::class)->execute(new UploadMediaInput(__DIR__.'/media/fable-'.$n.'.png', 'fable-demo-'.$n.'.png', $aid, allowDuplicate: true)), "Uploading picture $n");
        $id = is_array($item) ? $item['id'] : $item->id;
        $as(fn () => app(UpdateMediaDetails::class)->execute(new UpdateMediaDetailsInput($id, $aid, 'Fable demo picture '.$n, 'A soft field of colour: a demo picture for the theme’s screenshots.', null, null)), "Describing picture $n");
        $state['media'][$n] = $id;
        $save();
        $say("media $n -> $id");
    }
}

if (in_array('content', $steps, true)) {
    foreach ($stories as [$key, $title, $deck, $pic, $sticky]) {
        if (isset($state['content'][$key])) {
            continue;
        }
        $mid = $state['media'][$pic] ?? throw new RuntimeException('Run the media step first.');
        // A media field stores {"id": <media id>}: the handbook does not say so (FINDINGS.md, #14).
        $fields = [F_EXCERPT => $deck, F_STICKY => $sticky, F_IMAGE => ['id' => $mid]];
        /** @var Content $c */
        $c = $as(fn () => app(CreateDraft::class)->execute(new CreateDraftInput('post', $title, ['type' => 'doc', 'content' => [['type' => 'paragraph']]], $fields, $aid)), "Drafting $key");
        $as(fn () => app(UpdateContentDetails::class)->execute(new UpdateContentDetailsInput($c->id, (string) $c->working_revision_id, $aid, slug: 'fable-demo-'.$key)), "Naming $key");
        $c->refresh();
        $as(fn () => app(SaveDraft::class)->execute(new SaveDraftInput($c->id, $title, $body($deck), $fields, (string) $c->working_revision_id, $aid)), "Writing $key");
        $c->refresh();
        $as(fn () => app(PublishRevision::class)->execute(new PublishRevisionInput($c->id, (string) $c->working_revision_id, $aid)), "Publishing $key");
        $state['content'][$key] = $c->id;
        $save();
        $say("published fable-demo-$key");
    }
}

if (in_array('menus', $steps, true)) {
    $url = static fn (string $label, string $to, array $children = []): array => ['kind' => 'url', 'url' => $to, 'label' => $label, 'children' => $children];
    $topic = static fn (string $label): array => $url($label, '/?s='.urlencode(strtolower($label)));
    $trees = [
        'primary' => ['Fable: Sections', [
            $url('Garden', '/?s=garden', [$topic('Flowers'), $topic('Vegetables'), $topic('Lawn care')]),
            $url('Animals', '/?s=dog', [$topic('Dogs'), $topic('Cats'), $topic('Wildlife')]),
            $url('Home & Interiors', '/?s=room', [$topic('Decorating'), $topic('DIY')]),
            $url('Food', '/?s=cook', [$topic('Recipes'), $topic('Pantry')]),
            $url('Travel', '/?s=route', [$topic('Europe'), $topic('Rail')]),
            $url('Wellness', '/?s=sleep'),
            $url('Tech', '/?s=apps'),
        ]],
        'footer' => ['Fable: Company', [$url('About', '/'), $url('Editorial standards', '/'), $url('Corrections', '/'), $url('Contact', '/')]],
        'network' => ['Fable: Network', [$url('PawPrint', 'https://example.com/pawprint'), $url('RootedGarden', 'https://example.com/rooted'), $url('TableFork', 'https://example.com/tablefork')]],
        'social' => ['Fable: Social', [$url('Fable on Instagram', 'https://instagram.com/example'), $url('Fable on X', 'https://x.com/example'), $url('Fable on Pinterest', 'https://pinterest.com/example'), $url('RSS feed', '/feed/')]],
    ];
    foreach ($trees as $slot => [$name, $tree]) {
        $nav = \App\Domain\Menus\Models\Navigation::query()->where('name', $name)->first()
            ?? $as(fn () => app(CreateNavigation::class)->execute(new CreateNavigationInput($name, $aid)), "Making menu $name");
        $as(fn () => app(SaveNavigationItems::class)->execute(new SaveNavigationItemsInput($nav->id, $tree, $aid)), "Filling menu $name");
        $as(fn () => app(BindNavigationSlot::class)->execute(new BindNavigationSlotInput('egberipiribo', $slot, $nav->id, $aid)), "Placing menu $name");
        $say("menu $slot placed");
    }
}

if (in_array('regions', $steps, true)) {
    $card = static fn (string $heading, int $count): array => ['type' => 'featured-posts', 'attrs' => ['id' => strtoupper((string) Illuminate\Support\Str::ulid()), 'heading' => $heading, 'source' => 'latest', 'type' => 'post', 'count' => $count, 'images' => true]];
    $regions = [
        'home-most-read' => $card('Most read this week', 5),
        'home-sections' => $card('Garden', 2),
    ];
    foreach ($regions as $name => $node) {
        $slug = 'egberipiribo--'.$name;
        $c = Content::query()->where('type', 'region')->where('slug', $slug)->first()
            ?? $as(fn () => app(CreateRegion::class)->execute(new CreateRegionInput('egberipiribo', $name, $aid)), "Making region $name");
        $as(fn () => app(SaveDraft::class)->execute(new SaveDraftInput($c->id, $c->title, ['type' => 'doc', 'content' => [$node]], [], (string) $c->working_revision_id, $aid)), "Writing region $name");
        $c->refresh();
        $as(fn () => app(PublishRevision::class)->execute(new PublishRevisionInput($c->id, (string) $c->working_revision_id, $aid)), "Publishing region $name");
        $state['regions'][$name] = $c->id;
        $save();
        $say("region $name published");
    }
}

if (in_array('activate', $steps, true) || in_array('restore', $steps, true)) {
    $code = in_array('restore', $steps, true) ? ($steps[array_search('restore', $steps, true) + 1] ?? 'default') : 'egberipiribo';
    $as(fn () => app(ActivateTheme::class)->execute(new ActivateThemeInput($code, $aid)), "Activating $code");
    $say("$code is the site's theme");
}
