<x-filament-panels::page>
    <div class="gp-wrap">
        <div class="gp-head">
            <h2>Gate Portal</h2>
        </div>

        <iframe
            class="gp-frame"
            src="{{ route('gate.portal', ['embedded' => 1]) }}"
            title="Gate Portal"
            loading="lazy"
        ></iframe>
    </div>

    <style>
        .gp-wrap {
            display: grid;
            gap: .75rem;
        }

        .gp-head {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .gp-head h2 {
            margin: 0;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 800;
        }

        .gp-frame {
            width: 100%;
            min-height: 78vh;
            border: 1px solid #dbe5f2;
            border-radius: 12px;
            background: #fff;
        }
    </style>
</x-filament-panels::page>
