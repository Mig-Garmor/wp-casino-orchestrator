#!/usr/bin/env bash

set -euo pipefail

git submodule update --remote --merge

echo "Submodules updated to latest tracked remote branches."
echo "Review changes, then commit the updated submodule pointers:"
echo "  git status"
echo "  git add repos/wp-casino-theme repos/wp-casino-plugin"
echo "  git commit -m \"Update theme and plugin submodules\""