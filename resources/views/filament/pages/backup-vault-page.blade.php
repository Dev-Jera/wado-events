<x-filament-panels::page>
    @php
        $backups = $this->getBackups();
        $remoteDir = $this->getRemoteDirectory();
    @endphp

    <div style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:1rem;">
        <div>
            <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700;">Remote Backup Directory</div>
            <div style="font-size:.9rem;color:#0f172a;font-weight:600;word-break:break-all;">{{ $remoteDir }}</div>
        </div>

        <button
            type="button"
            wire:click="runRemoteBackup"
            style="background:#0a4fbe;color:#fff;border:none;padding:.6rem 1rem;border-radius:.6rem;font-weight:700;cursor:pointer;"
        >
            Run Remote Backup Now
        </button>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:.9rem;margin-bottom:1rem;">
        <label for="backup-search" style="display:block;font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700;margin-bottom:.45rem;">
            Search Backups
        </label>
        <input
            id="backup-search"
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Type file name, date, or keyword..."
            style="width:100%;border:1px solid #cbd5e1;border-radius:.6rem;padding:.6rem .75rem;font-size:.9rem;"
        />
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:auto;">
        <table style="width:100%;border-collapse:collapse;min-width:680px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="text-align:left;padding:.7rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">File</th>
                    <th style="text-align:left;padding:.7rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Last Modified</th>
                    <th style="text-align:right;padding:.7rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Size</th>
                    <th style="text-align:right;padding:.7rem .9rem;font-size:.74rem;color:#475569;text-transform:uppercase;letter-spacing:.08em;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($backups as $backup)
                    <tr style="border-top:1px solid #f1f5f9;">
                        <td style="padding:.75rem .9rem;font-weight:600;color:#0f172a;">{{ $backup['name'] }}</td>
                        <td style="padding:.75rem .9rem;color:#334155;">{{ $backup['modified_at']->format('Y-m-d H:i:s') }}</td>
                        <td style="padding:.75rem .9rem;color:#334155;text-align:right;">{{ number_format($backup['size_bytes'] / 1024, 1) }} KB</td>
                        <td style="padding:.75rem .9rem;text-align:right;">
                            <button
                                type="button"
                                wire:click="download('{{ addslashes($backup['name']) }}')"
                                style="background:#1e293b;color:#fff;border:none;padding:.45rem .75rem;border-radius:.5rem;font-size:.82rem;font-weight:700;cursor:pointer;"
                            >
                                Download
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding:1rem .9rem;text-align:center;color:#64748b;">
                            No backup files found in this directory.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
