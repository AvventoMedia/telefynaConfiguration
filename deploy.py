#!/usr/bin/env python3
"""
Telefyna Configuration v2 — FTP True Sync Deploy Script
Features:
- Smart Sync: Overwrites existing files on the server.
- True Pruning: Deletes remote orphans not found in the local repo.
- Safety First: Protects config.json, exports, .well-known, and system files.
- Interactive: Asks for confirmation before deleting anything.
"""

import ftplib
import os
import sys
import getpass

# ─── CONFIG ──────────────────────────────────────────────
FTP_HOST = "box5697.bluehost.com"
FTP_USER = "avvento@avventomedia.org"
# No hardcoded password — we prompt for it securely at runtime.
FTP_PASS = "" 
REMOTE_ROOT = "public_html/telefynaConfiguration2"

# Local folder to upload (this script's directory)
LOCAL_ROOT = os.path.dirname(os.path.abspath(__file__))

# ── EXCLUSIONS & PROTECTION ──

# Local files to NEVER upload (even if they exist)
EXCLUDES = {".git", ".ftpconfig", ".env", "deploy.py", "deploy.sh",
            "__pycache__", "node_modules", ".DS_Store", "Thumbs.db", ".gemini"}

# Remote items to NEVER delete (even if missing locally)
PROTECTED = {".well-known", "cgi-bin", ".tmb", "public_ftp", "config.json", "exports"}

# Dotfiles that SHOULD be uploaded (exceptions to the "skip dotfiles" rule)
DOTFILE_WHITELIST = {".htaccess"}

# ─── HELPERS ─────────────────────────────────────────────

def get_local_tree(root):
    """Map the local file structure."""
    tree = {}
    for dirpath, dirnames, filenames in os.walk(root):
        rel_dir = os.path.relpath(dirpath, root)
        if rel_dir == ".": rel_dir = ""
        
        # Filter excludes
        parts = rel_dir.split(os.sep)
        if any(p in EXCLUDES or p.startswith(".") for p in parts if p):
            continue

        valid_files = [f for f in filenames if f not in EXCLUDES and (not f.startswith(".") or f in DOTFILE_WHITELIST)]
        if rel_dir or valid_files:
            tree[rel_dir] = valid_files
    return tree

def get_remote_items(ftp, path):
    """List remote items using MLSD for efficiency."""
    try:
        return list(ftp.mlsd(path))
    except Exception:
        return []

def ftp_mkdir_p(ftp, remote_dir):
    dirs = remote_dir.split("/")
    path = ""
    for d in dirs:
        if not d: continue
        path = f"{path}/{d}" if path else d
        try:
            ftp.mkd(path)
        except ftplib.error_perm:
            pass

def upload_file(ftp, local_path, remote_path):
    with open(local_path, "rb") as f:
        ftp.storbinary(f"STOR {remote_path}", f)

# ─── MAIN LOGIC ──────────────────────────────────────────

def main():
    global FTP_PASS
    print()
    print("╔═════════════════════════════════════════════════════╗")
    print("║  Telefyna Configuration v2 — FTP True Sync          ║")
    print("╚═════════════════════════════════════════════════════╝\n")

    # Prompt for password securely
    if not FTP_PASS:
        FTP_PASS = getpass.getpass(f"🔐 Enter FTP password for {FTP_USER}: ")

    print(f"📡 Connecting to {FTP_HOST}...")
    try:
        ftp = ftplib.FTP()
        ftp.connect(FTP_HOST, 21, timeout=30)
        ftp.login(FTP_USER, FTP_PASS)
        ftp.set_pasv(True)
        print("✅ Connected!\n")

        # Step 1: Upload / Replace
        print(f"🚀 Phase 1: Uploading & Replacing files to {REMOTE_ROOT}...")
        local_tree = get_local_tree(LOCAL_ROOT)
        
        for rel_dir, files in local_tree.items():
            remote_dir = f"{REMOTE_ROOT}/{rel_dir}" if rel_dir else REMOTE_ROOT
            ftp_mkdir_p(ftp, remote_dir)
            
            for f in files:
                l_path = os.path.join(LOCAL_ROOT, rel_dir, f)
                r_path = f"{remote_dir}/{f}"
                upload_file(ftp, l_path, r_path)
                print(f"   ⬆️  Uploaded: {r_path}")

        # Step 2: Pruning (Delete orphans)
        print("\n🔍 Phase 2: Scanning for orphans on server...")
        to_delete = []

        def scan_for_orphans(current_rel_path):
            remote_path = f"{REMOTE_ROOT}/{current_rel_path}" if current_rel_path else REMOTE_ROOT
            items = get_remote_items(ftp, remote_path)
            
            for name, attrs in items:
                if name in (".", ".."): continue
                
                # SAFETY: If item is in PROTECTED, EXCLUDES, or is a hidden dotfile, LEAVE IT ALONE.
                if name in PROTECTED or name in EXCLUDES or name.startswith("."): 
                    continue 
                
                rel_item_path = os.path.join(current_rel_path, name) if current_rel_path else name
                
                # Check if it exists locally
                path_parts = rel_item_path.split(os.path.sep)
                parent_dir = os.path.sep.join(path_parts[:-1]) if len(path_parts) > 1 else ""
                item_name = path_parts[-1]
                
                is_file = attrs['type'] == 'file'
                exists_locally = False
                if parent_dir in local_tree:
                    if is_file:
                        exists_locally = item_name in local_tree[parent_dir]
                    else:
                        # It's a directory, check if any tracked file exists inside it locally
                        exists_locally = any(d.startswith(rel_item_path) for d in local_tree.keys())

                if not exists_locally:
                    to_delete.append((rel_item_path, attrs['type']))
                elif attrs['type'] == 'dir':
                    scan_for_orphans(rel_item_path)

        scan_for_orphans("")

        if to_delete:
            print(f"\n⚠️  Found {len(to_delete)} orphan items on the server:")
            for path, ftype in to_delete:
                print(f"   [{ftype.upper()}] {path}")
            
            confirm = input("\nProceed with deletion of these items? [y/N]: ")
            if confirm.lower() == 'y':
                # Delete files first, then directories (ordered by depth)
                to_delete.sort(key=lambda x: x[0].count(os.sep), reverse=True)
                for rel_path, ftype in to_delete:
                    r_path = f"{REMOTE_ROOT}/{rel_path}"
                    try:
                        if ftype == 'dir':
                            ftp.rmd(r_path)
                            print(f"   🗑  Removed Folder: {r_path}")
                        else:
                            ftp.delete(r_path)
                            print(f"   🗑  Deleted File:   {r_path}")
                    except Exception as e:
                        print(f"   ❌ Error deleting {r_path}: {e}")
            else:
                print("⏭  Deletion skipped.")
        else:
            print("✅ Server is already clean. No orphans found.")

        ftp.quit()
        print("\n🎉 True Sync Complete!")
        print("🌐 Visit: https://avventomedia.org/telefynaConfiguration2\n")

    except Exception as e:
        print(f"❌ Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()
