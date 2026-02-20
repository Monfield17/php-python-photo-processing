# PHP + Python – ukázka AI zpracování fotek

Tento mini projekt ukazuje:

- dávkové zpracování fotek v PHP (`PhotoBatchProcessor`)
- volání Python skriptu z PHP (`PhotoAiRunner`)
- jednoduchý AI výstup z Pythonu (`analyze.py`)
- testovací skript (`test.php`)

## Spuštění

1. Mít nainstalovaný PHP a Python 3.
2. Nastavit oprávnění pro Python skript:
   ```bash
   chmod +x scripts/analyze.py
