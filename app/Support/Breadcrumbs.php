<?php

namespace App\Support;

use App\Models\CommerceItem;
use App\Models\Contract;
use App\Models\Quotation;
use App\Models\Template;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Breadcrumbs
{
    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    public static function items(?string $name = null, array $params = []): array
    {
        if ($name === null) {
            $route = request()->route();
            $name = $route ? (string) $route->getName() : '';
            $params = $route ? $route->parameters() : [];
        }

        $items = self::trail($name, $params);
        if ($items === []) {
            $items = [self::dash(), ['label' => self::fallbackLabel($name), 'url' => null]];
        }

        return self::normalize($items);
    }

    /**
     * @param  array<int, array{label: string, url: ?string}>  $items
     * @return array<int, array{label: string, url: ?string}>
     */
    private static function normalize(array $items): array
    {
        $clean = [];
        foreach ($items as $item) {
            $label = trim((string) ($item['label'] ?? ''));
            if ($label === '') {
                continue;
            }
            $clean[] = [
                'label' => $label,
                'url' => $item['url'] ?? null,
            ];
        }

        if ($clean === []) {
            $clean[] = self::dash();
        }

        $last = count($clean) - 1;
        $clean[$last]['url'] = null;

        return $clean;
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    private static function trail(string $name, array $p): array
    {
        $d = self::dash();
        $ai = ['label' => __('AI Manager'), 'url' => self::url('ai.applications')];
        $as = ['label' => __('Assistant'), 'url' => self::url('assistant')];
        $set = ['label' => __('Settings'), 'url' => self::url('settings')];

        switch ($name) {
            case 'dashboard':
                return [$d];
            case 'calendar.view':
                return [$d, self::now(__('Calendar'))];
            case 'gantt.view':
                return [$d, self::now(__('Gantt'))];
            case 'agent':
                return [$d, self::now(__('AI Chat'))];
            case 'ticket':
                return [$d, self::now(__('Ticket'))];
            case 'client':
                return [$d, self::now(__('Customers'))];
            case 'billing':
                return [$d, self::now(__('Billing'))];
            case 'message':
                return [$d, self::now(__('Messages'))];
            case 'assistant':
                return [$d, self::now(__('Assistant'))];
            case 'profile.show':
                return [$d, self::now(__('Profile'))];
            case 'teams.create':
                return [$d, self::now(__('Create team'))];
            case 'teams.show':
                return [$d, self::now(self::labelFor($p['team'] ?? null) ?: __('Team'))];
            case 'api-tokens.index':
                return [$d, self::now(__('API Tokens'))];
        }

        if (Str::startsWith($name, 'ai.')) {
            return self::aiTrail($name, $p, $d, $ai);
        }
        if (in_array($name, ['project', 'project.show', 'commercial', 'commercial.show', 'commercial.edit.show', 'commercial.sync', 'commercial.print', 'order', 'show.order', 'invoice', 'show.invoice', 'commission', 'show.commission'], true)) {
            return self::assistantTrail($name, $p, $d, $as);
        }
        if (Str::startsWith($name, 'user.') || $name === 'user.billing.invoice.show') {
            return self::userTrail($name, $p, $d);
        }
        if (in_array($name, ['settings', 'settings.show', 'settings.company.show', 'role.index', 'role.show', 'permission.index', 'flow.show', 'notification', 'notification.read'], true)) {
            return self::settingsTrail($name, $p, $d, $set);
        }
        if (in_array($name, ['template', 'create.template', 'view.template', 'show.template', 'edit.template'], true)) {
            return self::templateTrail($name, $p, $d);
        }
        if (in_array($name, ['report.index', 'report.show', 'reports.download', 'reports.view'], true)) {
            return self::reportTrail($name, $p, $d);
        }
        if (in_array($name, ['payment.deposit', 'payment.topup', 'invoice.topup'], true)) {
            return self::paymentTrail($name, $p, $d);
        }

        return self::guess($name, $p, $d);
    }

    private static function aiTrail(string $name, array $p, array $d, array $ai): array
    {
        $app = $p['application'] ?? null;
        $appLabel = self::labelFor($app);
        $appUrl = $app ? self::url('ai.applications.show', ['application' => $app]) : null;
        $apps = ['label' => __('Applications'), 'url' => self::url('ai.applications')];

        switch ($name) {
            case 'ai.applications':
                return [$d, $ai, self::now(__('Applications'))];
            case 'ai.applications.show':
                return [$d, $ai, $apps, self::now($appLabel ?: __('Application'))];
            case 'ai.applications.usage':
                return [$d, $ai, $apps, ['label' => $appLabel ?: __('Application'), 'url' => $appUrl], self::now(__('Usage'))];
            case 'ai.applications.requests':
                return [$d, $ai, $apps, ['label' => $appLabel ?: __('Application'), 'url' => $appUrl], self::now(__('Requests'))];
            case 'ai.applications.test':
                return [$d, $ai, $apps, ['label' => $appLabel ?: __('Application'), 'url' => $appUrl], self::now(__('Test'))];
            case 'ai.usage':
                return [$d, $ai, self::now(__('Usage'))];
            case 'ai.docs':
                $docs = ['label' => __('API Docs'), 'url' => self::url('ai.docs')];
                if (request('tab') === 'docs') {
                    return [$d, $ai, $docs, self::now(__('Docs'))];
                }

                return [$d, $ai, $docs, self::now(__('Endpoint'))];
            default:
                return [$d, $ai, self::now(self::word(Str::after($name, 'ai.')))];
        }
    }

    private static function assistantTrail(string $name, array $p, array $d, array $as): array
    {
        switch ($name) {
            case 'project':
                return [$d, $as, self::now(__('Project'))];
            case 'project.show':
                return [$d, $as, ['label' => __('Project'), 'url' => self::url('project')], self::now(self::labelFor($p['project'] ?? null) ?: __('Project'))];
            case 'commercial':
                return [$d, $as, self::now(__('Commercial'))];
            case 'commercial.show':
                return [$d, $as, ['label' => __('Commercial'), 'url' => self::url('commercial')], self::now(self::commercialKey($p['key'] ?? ''))];
            case 'commercial.edit.show':
                $key = (string) ($p['key'] ?? '');
                $id = $p['id'] ?? null;

                return [
                    $d,
                    $as,
                    ['label' => __('Commercial'), 'url' => self::url('commercial')],
                    ['label' => self::commercialKey($key), 'url' => self::url('commercial.show', ['key' => $key])],
                    self::now(self::commercialEntity($key, $id)),
                ];
            case 'commercial.sync':
                return [$d, $as, ['label' => __('Commercial'), 'url' => self::url('commercial')], self::now(__('Sync'))];
            case 'commercial.print':
                return [$d, $as, ['label' => __('Commercial'), 'url' => self::url('commercial')], self::now(__('Print'))];
            case 'order':
                return [$d, $as, self::now(__('Order'))];
            case 'show.order':
                return [$d, $as, ['label' => __('Order'), 'url' => self::url('order')], self::now(self::labelFor($p['order'] ?? null) ?: __('Order'))];
            case 'invoice':
                return [$d, $as, self::now(__('Invoice'))];
            case 'show.invoice':
                return [$d, $as, ['label' => __('Invoice'), 'url' => self::url('invoice')], self::now(self::labelFor($p['invoice'] ?? null) ?: __('Invoice'))];
            case 'commission':
                return [$d, $as, self::now(__('Commissions'))];
            case 'show.commission':
                return [$d, $as, ['label' => __('Commissions'), 'url' => self::url('commission')], self::now(self::labelFor($p['commission'] ?? null) ?: __('Commission'))];
        }

        return [$d, $as];
    }

    private static function userTrail(string $name, array $p, array $d): array
    {
        $users = ['label' => __('Users'), 'url' => self::url('user.index')];
        $user = $p['user'] ?? null;
        $userLabel = self::labelFor($user) ?: __('User');
        $userUrl = $user ? self::url('user.show', ['user' => $user]) : null;

        switch ($name) {
            case 'user.index':
                return [$d, self::now(__('Users'))];
            case 'user.show':
                return [$d, $users, self::now($userLabel)];
            case 'user.show.balance':
                return [$d, $users, ['label' => $userLabel, 'url' => $userUrl], self::now(__('Balance'))];
            case 'user.show.profile':
                return [$d, $users, ['label' => $userLabel, 'url' => $userUrl], self::now(__('Profile'))];
            case 'user.billing.index':
            case 'user.billing.generate':
                return [$d, self::now(__('Billing'))];
            case 'user.billing.invoice.show':
                return [$d, ['label' => __('Billing'), 'url' => self::url('user.billing.index')], self::now(self::labelFor($p['billing'] ?? null) ?: __('Invoice'))];
        }

        return [$d, $users];
    }

    private static function settingsTrail(string $name, array $p, array $d, array $set): array
    {
        switch ($name) {
            case 'settings':
                return [$d, self::now(__('Settings'))];
            case 'settings.show':
                return [$d, $set, self::now(self::settingsPage($p['page'] ?? ''))];
            case 'settings.company.show':
                return [
                    $d,
                    $set,
                    ['label' => __('Company'), 'url' => self::url('settings.show', ['page' => 'company'])],
                    self::now(self::labelFor($p['company'] ?? null) ?: __('Company')),
                ];
            case 'role.index':
                return [$d, $set, self::now(__('Role'))];
            case 'role.show':
                return [$d, $set, ['label' => __('Role'), 'url' => self::url('role.index')], self::now(self::labelFor($p['role'] ?? null) ?: __('Role'))];
            case 'permission.index':
                return [$d, $set, self::now(__('Menu'))];
            case 'flow.show':
                return [$d, $set, ['label' => __('Menu'), 'url' => self::url('permission.index')], self::now(self::word((string) ($p['model'] ?? __('Flow'))))];
            case 'notification':
                return [$d, $set, self::now(__('Notification'))];
            case 'notification.read':
                return [$d, $set, ['label' => __('Notification'), 'url' => self::url('notification')], self::now(self::notificationLabel($p['notification'] ?? null))];
        }

        return [$d, $set];
    }

    private static function templateTrail(string $name, array $p, array $d): array
    {
        $list = ['label' => __('Templates'), 'url' => self::url('template')];

        switch ($name) {
            case 'template':
                return [$d, self::now(__('Templates'))];
            case 'create.template':
                return [$d, $list, self::now(__('Create'))];
            case 'view.template':
                return [$d, $list, self::now(__('Tree'))];
            case 'show.template':
                return [$d, $list, self::now(self::templateLabel($p['uuid'] ?? $p['template'] ?? null))];
            case 'edit.template':
                return [$d, $list, ['label' => self::templateLabel($p['template'] ?? null), 'url' => null], self::now(__('Edit'))];
        }

        return [$d, $list];
    }

    private static function reportTrail(string $name, array $p, array $d): array
    {
        $list = ['label' => __('Report'), 'url' => self::url('report.index')];

        switch ($name) {
            case 'report.index':
                return [$d, self::now(__('Report'))];
            case 'report.show':
                return [$d, $list, self::now(self::reportKey($p['key'] ?? ''))];
            case 'reports.view':
            case 'reports.download':
                return [$d, $list, self::now(__('File'))];
        }

        return [$d, $list];
    }

    private static function paymentTrail(string $name, array $p, array $d): array
    {
        $bal = ['label' => __('Balance'), 'url' => self::url('payment.deposit')];

        switch ($name) {
            case 'payment.deposit':
                return [$d, self::now(__('Balance'))];
            case 'payment.topup':
                return [$d, $bal, self::now(__('Top up'))];
            case 'invoice.topup':
                return [$d, $bal, ['label' => __('Top up'), 'url' => self::url('payment.topup')], self::now(__('Invoice'))];
        }

        return [$d, $bal];
    }

    /**
     * @return array<int, array{label: string, url: ?string}>
     */
    private static function guess(string $name, array $p, array $d): array
    {
        if ($name === '') {
            return [$d];
        }

        $items = [$d];
        $parts = explode('.', $name);
        $acc = [];
        $last = count($parts) - 1;

        foreach ($parts as $i => $part) {
            $acc[] = $part;
            $candidate = implode('.', $acc);
            if (in_array($part, ['index', 'show'], true) && $i === $last) {
                $entity = self::entityFromParams($p);
                if ($entity !== null) {
                    $items[] = self::now($entity);
                    continue;
                }
            }
            $label = $i === $last ? (self::entityFromParams($p) ?: self::word($part)) : self::word($part);
            $items[] = [
                'label' => $label,
                'url' => $i === $last ? null : self::url($candidate),
            ];
        }

        return $items;
    }

    private static function dash(): array
    {
        return ['label' => __('Dashboard'), 'url' => self::url('dashboard')];
    }

    private static function now(string $label): array
    {
        return ['label' => $label, 'url' => null];
    }

    private static function url(string $name, $params = []): ?string
    {
        try {
            if (! Route::has($name)) {
                return null;
            }

            return route($name, $params);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function labelFor($value): ?string
    {
        if ($value instanceof Model) {
            foreach (['name', 'title', 'quote_no', 'no', 'code', 'sku', 'email'] as $attr) {
                $raw = $value->getAttribute($attr);
                if ($raw !== null && $raw !== '') {
                    return (string) $raw;
                }
            }

            return '#'.$value->getKey();
        }

        if (is_string($value) || is_numeric($value)) {
            $text = trim((string) $value);

            return $text === '' ? null : $text;
        }

        return null;
    }

    private static function entityFromParams(array $params): ?string
    {
        foreach ($params as $value) {
            if ($value instanceof Model) {
                return self::labelFor($value);
            }
        }

        return null;
    }

    private static function commercialKey($key): string
    {
        $map = [
            'item' => __('Product'),
            'quotation' => __('Quotation'),
            'contract' => __('Contract'),
        ];

        return $map[(string) $key] ?? self::word((string) $key);
    }

    private static function commercialEntity(string $key, $id): string
    {
        $fromModel = self::labelFor($id);
        if ($fromModel !== null && ! is_string($id) && ! is_numeric($id)) {
            return $fromModel;
        }

        $public = is_string($id) || is_numeric($id) ? (string) $id : null;
        if ($public === null) {
            return __('Detail');
        }

        try {
            if ($key === 'quotation') {
                $row = Quotation::findPublic($public);

                return $row ? (self::labelFor($row) ?: __('Quotation')) : $public;
            }
            if ($key === 'contract') {
                $row = Contract::findPublic($public);

                return $row ? (self::labelFor($row) ?: __('Contract')) : $public;
            }
            $row = CommerceItem::findPublic($public);

            return $row ? (self::labelFor($row) ?: __('Product')) : $public;
        } catch (\Throwable $e) {
            return $public;
        }
    }

    private static function settingsPage($page): string
    {
        $map = [
            'company' => __('Company'),
            'role' => __('Role'),
            'notification' => __('Notification'),
        ];

        return $map[(string) $page] ?? self::word((string) $page);
    }

    private static function reportKey($key): string
    {
        $map = [
            'billing' => __('Billing'),
            'request' => __('Log Chat'),
            'sms' => __('Log SMS'),
            'log' => __('Log API Request'),
        ];

        return $map[(string) $key] ?? self::word((string) $key);
    }

    private static function templateLabel($value): string
    {
        $from = self::labelFor($value);
        if ($from !== null && $value instanceof Model) {
            return $from;
        }

        $key = is_object($value) ? null : (string) $value;
        if ($key === null || $key === '') {
            return __('Template');
        }

        try {
            $name = Template::query()->where('uuid', $key)->orWhere('id', $key)->value('name');

            return $name ? (string) $name : $key;
        } catch (\Throwable $e) {
            return $key;
        }
    }

    private static function notificationLabel($value): string
    {
        if ($value instanceof Model) {
            $text = $value->getAttribute('notification') ?: $value->getAttribute('title');
            if (is_string($text) && $text !== '') {
                return Str::limit(trim(strip_tags($text)), 32, '');
            }
        }

        return __('Notification');
    }

    private static function word(string $value): string
    {
        $value = str_replace(['-', '_'], ' ', $value);
        $known = [
            'ai' => __('AI Manager'),
            'docs' => __('API Docs'),
            'show' => __('Detail'),
            'index' => __('List'),
            'create' => __('Create'),
            'edit' => __('Edit'),
            'usage' => __('Usage'),
        ];
        $lower = strtolower($value);

        return $known[$lower] ?? Str::title($value);
    }

    private static function fallbackLabel(string $name): string
    {
        if ($name === '') {
            return __('Page');
        }

        return self::word(str_replace('.', ' ', $name));
    }
}
