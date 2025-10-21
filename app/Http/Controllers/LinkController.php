<?php

namespace App\Http\Controllers;

use App\Models\Click;
use App\Models\Link;
use App\Models\LinkDestination;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class LinkController extends Controller
{
    public function __construct()
    {
    }

    private function ensureAuthed(Request $request)
    {
        if (!$request->session()->get('authed')) {
            abort(401);
        }
    }

    public function store(Request $request)
    {
        $this->ensureAuthed($request);
        $data = $request->validate([
            'destination' => 'nullable|url',
            'is_rotator' => 'required|boolean',
            'code' => 'nullable|alpha_num|max:16',
            'rotator.*.url' => 'nullable|url',
            'rotator.*.weight' => 'nullable|integer|min:1|max:100',
        ]);

        $code = $data['code'] ?? Str::random(6);
        $link = Link::create([
            'code' => $code,
            'destination' => $data['destination'] ?? null,
            'is_rotator' => $data['is_rotator'],
        ]);

        if ($data['is_rotator'] && $request->has('rotator')) {
            foreach ($request->input('rotator', []) as $row) {
                if (!empty($row['url'])) {
                    LinkDestination::create([
                        'link_id' => $link->id,
                        'url' => $row['url'],
                        'weight' => (int) ($row['weight'] ?? 1),
                    ]);
                }
            }
        }

        return redirect()->route('dashboard')->with('ok', 'Link created: ' . url('/' . $link->code));
    }

    public function showCreate(Request $request)
    {
        $this->ensureAuthed($request);
        return view('dashboard.create');
    }

    public function redirect(Request $request, string $code)
    {
        $link = Link::where('code', $code)->firstOrFail();

        $ip = $request->ip() ?? '';
        $ua = $request->userAgent() ?? '';

        // Decide destination
        $dest = $link->destination;
        if ($link->is_rotator) {
            $choices = $link->destinations()->get(['url', 'weight']);
            $sum = max(1, $choices->sum('weight'));
            $roll = random_int(1, $sum);
            $acc = 0;
            foreach ($choices as $c) {
                $acc += (int) $c->weight;
                if ($roll <= $acc) { $dest = $c->url; break; }
            }
            if (!$dest && $choices->count()) { $dest = $choices->first()->url; }
        }

        // Collect device/browser via jenssegers/agent
        $agent = new Agent();
        $agent->setUserAgent($ua);
        $device = $agent->device() ?: ($agent->isDesktop() ? 'Desktop' : ($agent->isPhone() ? 'Phone' : 'Other'));
        $browser = $agent->browser() ?: 'Unknown';

        // Country via stopbot iplookup (best-effort)
        $country = null;
    $geo = app(\App\Services\StopBotService::class)->ipLookup($ip);
        if (is_array($geo)) {
            $country = $geo['countryCode'] ?? ($geo['country_code'] ?? null);
            if (is_string($country) && strlen($country) > 2) { $country = substr($country, 0, 2); }
        }

        Click::create([
            'link_id' => $link->id,
            'ip' => $ip,
            'country' => $country,
            'device' => $device,
            'browser' => $browser,
            'user_agent' => $ua,
            'referer' => $request->headers->get('referer'),
        ]);

        if (!$dest) {
            abort(404, 'No destination');
        }
        return redirect()->away($dest);
    }
}
