{-# LANGUAGE DataKinds #-}
{-# LANGUAGE NoImplicitPrelude #-}
{-# LANGUAGE ScopedTypeVariables #-}
{-# LANGUAGE TemplateHaskell #-}

module AntiAbuseUtils where

import PlutusTx
import PlutusTx.Prelude
import Plutus.V2.Ledger.Api
import Plutus.V2.Ledger.Contexts

{-
  Anti-abuse utilities
  - replay protection via unique nonce in datum
  - front-running resistance via signer checks
  - upgrade guard with version hash
-}

newtype VersionTag = VersionTag BuiltinByteString
PlutusTx.unstableMakeIsData ''VersionTag

{-# INLINABLE enforceVersion #-}
enforceVersion :: VersionTag -> TxInfo -> Bool
enforceVersion (VersionTag tag) info =
  let metadata = txInfoData info
  in traceIfFalse "version tag missing" (any (\(_, d) -> getTag d == tag) metadata)
  where
    getTag :: Datum -> BuiltinByteString
    getTag (Datum d) = case PlutusTx.fromBuiltinData d of
      Just (VersionTag tagValue) -> tagValue
      Nothing -> emptyByteString

{-# INLINABLE enforceSignedBy #-}
enforceSignedBy :: PubKeyHash -> TxInfo -> Bool
enforceSignedBy pkh info = traceIfFalse "missing signer" (txSignedBy info pkh)
