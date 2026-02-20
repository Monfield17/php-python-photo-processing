#!/usr/bin/env python3
import sys
import json

def analyze(path):
    return {
        "objects": [
            {
                "id": "object_1",
                "confidence": 0.97,
                "x1": 120,
                "y1": 140,
                "x2": 260,
                "y2": 300
            }
        ]
    }

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"error": "Missing image path"}))
        sys.exit(1)

    image_path = sys.argv[1]
    result = analyze(image_path)
    print(json.dumps(result))
